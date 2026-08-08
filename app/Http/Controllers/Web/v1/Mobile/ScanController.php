<?php

namespace App\Http\Controllers\Web\v1\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Shared\QrTokenResolverService;
use Dedoc\Scramble\Attributes\Group;

#[Group('Web/Scan-resolver')]
class ScanController extends Controller
{
    public function __construct(
        private readonly QrTokenResolverService $resolver
    ){}

    public function show(string $token)
    {
        $leadable = $this->resolver->resolve($token);

        return view('scan-landing', ['leadable' => $leadable]);
    }
}
