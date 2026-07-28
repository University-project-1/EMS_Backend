<?php

namespace App\Services\SystemUser\Admin;

use App\Enum\ReportStatus;
use App\Models\Report;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReportService
{
    public function resolved(Report $report, array $data){
        if ($report->status !== ReportStatus::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        $report->update([
            'admin_notes' => $data['notes'] ?? null,
            'resolved_by' => auth('system')->id(),
            'status' => ReportStatus::RESOLVED
        ]);
    }

    public function rejected(Report $report, array $data){
        if ($report->status !== ReportStatus::PENDING) {
            throw new HttpException(400, __('validation.invalid_status'));
        }

        $report->update([
            'admin_notes' => $data['notes'] ?? null,
            'resolved_by' => auth('system')->id(),
            'status' => ReportStatus::REJECTED
        ]);
    }
}
