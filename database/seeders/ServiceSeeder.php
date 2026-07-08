<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Booth Design', 'price' => 150.00, 'is_active' => true],
            ['name' => 'Catering', 'price' => 250.00, 'is_active' => true],
            ['name' => 'Audio Visual Support', 'price' => 300.00, 'is_active' => true],
            ['name' => 'Security Staff', 'price' => 180.00, 'is_active' => true],
            ['name' => 'Logistics Support', 'price' => 220.00, 'is_active' => true],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
