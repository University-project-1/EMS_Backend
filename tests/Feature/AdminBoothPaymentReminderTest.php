<?php

use App\Enum\BusinessSectors;
use App\Enum\Status;
use App\Models\BoothRequest;
use App\Models\Company;
use App\Models\Hall;
use App\Notifications\SystemUser\Exhibitor\BoothPaymentReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('admin can send a queued payment reminder for an approved booth request', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $company = paymentReminderCompany();
    $hall = Hall::query()->create([
        'number' => 'HALL-PAYMENT-701',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-PAYMENT-701',
        'area' => 25,
        'price' => 500,
        'company_id' => $company->id,
    ]);
    $request = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $company->id,
        'system_user_id' => $exhibitor->id,
        'final_price' => 500,
        'status' => Status::APPROVED,
    ]);

    $this->actingAs($admin, 'system')
        ->postJson("/api/v1/admin/booths/requests/payment-reminder/{$request->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    Notification::assertSentTo(
        $exhibitor,
        BoothPaymentReminderNotification::class,
        fn (BoothPaymentReminderNotification $notification): bool => $notification->boothRequest->is($request),
    );
});

test('admin cannot send a payment reminder for a non-approved booth request', function (): void {
    Notification::fake();

    $admin = $this->createAdministrator();
    $exhibitor = $this->createExhibitor();
    $company = paymentReminderCompany('Invalid Payment Reminder Company');
    $hall = Hall::query()->create([
        'number' => 'HALL-PAYMENT-702',
        'area' => 1000,
        'type' => 'exhibition',
    ]);
    $booth = $hall->booths()->create([
        'number' => 'B-PAYMENT-702',
        'area' => 25,
        'price' => 500,
    ]);
    $request = BoothRequest::query()->create([
        'booth_id' => $booth->id,
        'company_id' => $company->id,
        'system_user_id' => $exhibitor->id,
        'final_price' => 500,
        'status' => Status::PENDING,
    ]);

    $this->actingAs($admin, 'system')
        ->postJson("/api/v1/admin/booths/requests/payment-reminder/{$request->id}")
        ->assertStatus(400);

    Notification::assertNothingSent();
});

function paymentReminderCompany(string $name = 'Payment Reminder Company'): Company
{
    return Company::query()->create([
        'name' => $name,
        'business_sector' => BusinessSectors::TECH->value,
        'social_links' => [],
        'phone' => '+963991000000',
        'year_founded' => 2020,
        'description' => 'A company used for payment reminder tests.',
        'status' => Status::APPROVED->value,
    ]);
}
