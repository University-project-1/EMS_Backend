<?php

use App\Http\Resources\SystemUser\Admin\VolunteerApplicationResource;
use App\Models\VolunteerApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('keeps the administration collection resource compact until detail relations are loaded', function (): void {
    $application = VolunteerApplication::factory()->create();

    $compact = VolunteerApplicationResource::make($application)->resolve(Request::create('/'));

    expect($compact)
        ->toHaveKeys(['id', 'full_name', 'email', 'phone', 'status', 'created_at'])
        ->not->toHaveKey('motivation')
        ->not->toHaveKey('cv')
        ->not->toHaveKey('review_note')
        ->not->toHaveKey('whatsapp_notification');

    $details = VolunteerApplicationResource::make($application->load(['media', 'reviewer']))
        ->resolve(Request::create('/'));

    expect($details)
        ->toHaveKey('motivation')
        ->toHaveKey('education_or_occupation')
        ->toHaveKey('cv')
        ->toHaveKey('review_note')
        ->toHaveKey('whatsapp_notification');
});

it('persists the explicitly selected volunteer interface language', function (): void {
    $this->from(route('volunteer.application.create'))
        ->get(route('volunteer.application.locale', ['locale' => 'en']))
        ->assertRedirect(route('volunteer.application.create'))
        ->assertCookie('volunteer_locale', 'en');

    $this->withCookie('volunteer_locale', 'en')
        ->get(route('volunteer.application.create'))
        ->assertOk()
        ->assertSee('Volunteer application')
        ->assertSee('lang="en"', false)
        ->assertSee('dir="ltr"', false);
});
