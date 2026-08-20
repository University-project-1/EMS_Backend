<?php

use App\Models\Hall;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('an authenticated visitor can read an empty cursor-paginated lead history', function (): void {
    $visitor = $this->createVisitor();

    $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/leads/history')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.data', [])
        ->assertJsonPath('data.per_page', 10)
        ->assertJsonPath('data.next_cursor', null)
        ->assertJsonPath('data.previous_cursor', null)
        ->assertJsonPath('data.has_more_pages', false);
});

test('an authenticated visitor can move through lead history using a cursor', function (): void {
    $visitor = $this->createVisitor();
    $hall = Hall::query()->create([
        'number' => 'HALL-LEAD-HISTORY',
        'area' => 1000,
        'type' => 'exhibition',
    ]);

    foreach (range(1, 11) as $position) {
        $booth = $hall->booths()->create([
            'number' => "LEAD-{$position}",
            'area' => 25,
            'price' => 500,
        ]);
        $lead = $booth->leads()->create(['user_id' => $visitor->id]);
        $lead->forceFill(['created_at' => now()->subMinutes(11 - $position)])->save();
    }

    $firstPage = $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/leads/history?per_page=10')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(10, 'data.data')
        ->assertJsonPath('data.per_page', 10)
        ->assertJsonPath('data.has_more_pages', true)
        ->assertJsonPath('data.data.0.type', 'booth');

    $nextCursor = $firstPage->json('data.next_cursor');

    expect($nextCursor)->toBeString()->not->toBeEmpty();

    $secondPage = $this->actingAs($visitor, 'mobile')
        ->getJson('/api/v1/visitor/leads/history?per_page=10&cursor='.urlencode($nextCursor))
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data')
        ->assertJsonPath('data.per_page', 10)
        ->assertJsonPath('data.has_more_pages', false);

    expect($secondPage->json('data.previous_cursor'))->toBeString()->not->toBeEmpty();
});
