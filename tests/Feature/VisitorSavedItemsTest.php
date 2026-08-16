<?php

use App\Models\Booth;
use App\Models\Event;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Support\CreatesActors;

uses(DatabaseMigrations::class, CreatesActors::class);

test('visitor can toggle an event in their saved items without affecting other records', function (): void {
    $this->seed(DatabaseSeeder::class);

    $visitor = $this->createVisitor();
    $event = Event::query()->firstOrFail();

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);

    $this->postJson("/api/v1/visitor/saved/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseMissing('saved', [
        'user_id' => $visitor->id,
        'savedable_type' => Event::class,
        'savedable_id' => $event->id,
    ]);
});

test('visitor can retrieve booths they have saved', function (): void {
    $this->seed(DatabaseSeeder::class);

    $visitor = $this->createVisitor();
    $booth = Booth::query()->firstOrFail();

    $this->actingAs($visitor, 'mobile')
        ->postJson("/api/v1/visitor/saved/booths/{$booth->id}")
        ->assertOk();

    $this->getJson('/api/v1/visitor/saved/booths')
        ->assertOk()
        ->assertJsonPath('status', true)
        ->assertJsonCount(1, 'data.data');
});
