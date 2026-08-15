<?php

use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Jobs\SendVolunteerAcceptanceWhatsappJob;
use App\Models\SystemUser;
use App\Models\VolunteerApplication;
use App\Services\Shared\UltraMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('renders the volunteer application page in the requested locale', function (): void {
    $this->withHeader('Accept-Language', 'en')
        ->get(route('volunteer.application.create'))
        ->assertOk()
        ->assertSee('Volunteer application')
        ->assertSee('dir="ltr"', false);
});

it('stores a pending application and attaches the CV through media library', function (): void {
    Storage::fake('local');

    $response = $this->post(route('volunteer.application.store'), [
        'full_name' => 'متطوع تجريبي',
        'email' => 'volunteer@example.test',
        'phone' => '+963944123456',
        'cv' => UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4
1 0 obj
<< /Type /Catalog >>
endobj
trailer
<< /Root 1 0 R >>
%%EOF'),
        'motivation' => 'أرغب بالمساهمة في تنظيم المعرض وخدمة الزوار بصورة احترافية.',
        'education_or_occupation' => 'طالب هندسة معلوماتية.',
        'skills' => 'التواصل والتنظيم.',
        'city' => 'دمشق',
        'privacy_consent' => true,
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('volunteer.application.received'));

    $application = VolunteerApplication::query()->firstOrFail();
    $cv = $application->getFirstMedia(VolunteerApplication::CV_COLLECTION);

    expect($application->status)->toBe(Status::PENDING);
    expect($cv)->not->toBeNull();
    expect($cv->disk)->toBe('local');
    Storage::disk('local')->assertExists($cv->getPathRelativeToRoot());
});

it('localizes validation errors for the public form', function (): void {
    $this->withHeader('Accept-Language', 'en')
        ->from(route('volunteer.application.create'))
        ->post(route('volunteer.application.store'), [
            'full_name' => 'Test Volunteer',
            'email' => 'volunteer@example.test',
            'phone' => '+963944123456',
            'cv' => UploadedFile::fake()->create('cv.exe', 100, 'application/octet-stream'),
            'motivation' => 'I would like to support visitors and contribute to a well organised exhibition.',
            'education_or_occupation' => 'Computer science student.',
            'privacy_consent' => true,
        ])
        ->assertRedirect(route('volunteer.application.create'))
        ->assertSessionHasErrors(['cv' => 'Only PDF, DOC, or DOCX files are allowed.']);
});

it('sends the official WhatsApp acceptance message through the shared client', function (): void {
    Storage::fake('local');
    config()->set('services.ultramsg.instance_id', 'instance-id');
    config()->set('services.ultramsg.token', 'token');
    Http::fake(['https://api.ultramsg.com/*' => Http::response(['sent' => true])]);

    $application = VolunteerApplication::factory()->create(['status' => Status::APPROVED]);

    (new SendVolunteerAcceptanceWhatsappJob($application->id))->handle(app(UltraMessageService::class));

    Http::assertSent(function ($request) use ($application): bool {
        return str_contains($request->url(), '/messages/chat')
            && $request['to'] === $application->phone
            && str_contains($request['body'], config('volunteer.whatsapp_group_url'));
    });

    expect($application->fresh()->whatsapp_notification_sent_at)->not->toBeNull();
});

it('serves the CV only through the protected administrator endpoint', function (): void {
    Storage::fake('local');

    $application = VolunteerApplication::factory()->create();
    $application
        ->addMedia(UploadedFile::fake()->createWithContent('cv.pdf', '%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF'))
        ->toMediaCollection(VolunteerApplication::CV_COLLECTION);

    $url = route('admin.volunteer-applications.cv', $application);

    $this->getJson($url)->assertUnauthorized();

    $administrator = SystemUser::query()->create([
        'name' => 'Volunteer Admin',
        'email' => 'volunteer-admin@example.test',
        'password' => 'password',
        'type' => SystemUserType::ADMIN,
        'email_verified_at' => now(),
    ]);

    $this->actingAs($administrator, 'system')
        ->get($url)
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
