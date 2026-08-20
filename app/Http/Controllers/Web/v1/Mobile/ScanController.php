<?php

namespace App\Http\Controllers\Web\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Booth;
use App\Services\Shared\QrTokenResolverService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Web/Scan-resolver')]
class ScanController extends Controller
{
    public function __construct(
        private readonly QrTokenResolverService $resolver
    ) {}

    public function show(string $token)
    {
        $leadable = $this->resolver->resolve($token);

        $companyLogoUrl = null;

        if ($leadable instanceof Booth) {
            $leadable->loadMissing('company');
            $companyLogoUrl = $leadable->company?->getFirstMediaUrl('logo') ?: null;
        }

        return view('scan-landing', [
            'leadable' => $leadable,
            'companyLogoUrl' => $companyLogoUrl,
        ]);
    }
}
