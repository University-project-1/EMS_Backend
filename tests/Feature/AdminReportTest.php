<?php

use App\Enum\ReportStatus;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesActors;

uses(RefreshDatabase::class, CreatesActors::class);

test('administrator can resolve a pending report and cannot resolve it twice', function (): void {
    $administrator = $this->createAdministrator();
    $visitor = $this->createVisitor();
    $report = Report::query()->create([
        'reporter_type' => $visitor::class,
        'reporter_id' => $visitor->id,
        'title' => 'Inappropriate event description',
        'description' => 'The published description contains inappropriate content.',
        'status' => ReportStatus::PENDING->value,
    ]);

    $this->actingAs($administrator, 'system')
        ->postJson("/api/v1/admin/reports/{$report->id}/resolved", [
            'notes' => 'Content reviewed and corrected.',
        ])
        ->assertOk()
        ->assertJsonPath('status', true);

    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'status' => ReportStatus::RESOLVED->value,
        'resolved_by' => $administrator->id,
        'admin_notes' => 'Content reviewed and corrected.',
    ]);

    $this->postJson("/api/v1/admin/reports/{$report->id}/resolved")
        ->assertBadRequest();
});
