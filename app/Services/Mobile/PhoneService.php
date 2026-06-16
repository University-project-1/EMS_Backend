<?php

namespace App\Services\Mobile;
use Propaganistas\LaravelPhone\PhoneNumber;

class PhoneService
{
    public function normalize(string $phone): string
    {
        return (string) new PhoneNumber($phone);    
    }
}
