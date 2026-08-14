<?php

namespace App\Services\Mobile;

use App\Models\Lead;
use App\Models\User;
use App\Services\Shared\QrTokenResolverService;

class LeadService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private readonly QrTokenResolverService $resolver
    ){}

    public function registerScan(User $user, string $token){
        $leadable = $this->resolver->resolve($token);
        $lead = Lead::firstOrCreate([
            'user_id' => $user->id,
            'leadable_type' => $leadable::class,
            'leadable_id' => $leadable->id
        ]);
        return $lead;
    }
}
