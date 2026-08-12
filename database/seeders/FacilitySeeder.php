<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * @var list<array{number: string, gender: string, svg_id: string, type: string}>
     */
    private const FACILITIES = [
        ['number' => 'WC-NORTH-WEST-M', 'gender' => 'male', 'svg_id' => 'facility-wc-north-west-m', 'type' => 'bathroom'],
        ['number' => 'WC-NORTH-WEST-F', 'gender' => 'female', 'svg_id' => 'facility-wc-north-west-f', 'type' => 'bathroom'],
        ['number' => 'WC-NORTH-EAST-M', 'gender' => 'male', 'svg_id' => 'facility-wc-north-east-m', 'type' => 'bathroom'],
        ['number' => 'WC-NORTH-EAST-F', 'gender' => 'female', 'svg_id' => 'facility-wc-north-east-f', 'type' => 'bathroom'],
        ['number' => 'WC-SOUTH-WEST-M', 'gender' => 'male', 'svg_id' => 'facility-wc-south-west-m', 'type' => 'bathroom'],
        ['number' => 'WC-SOUTH-WEST-F', 'gender' => 'female', 'svg_id' => 'facility-wc-south-west-f', 'type' => 'bathroom'],
        ['number' => 'WC-SOUTH-EAST-M', 'gender' => 'male', 'svg_id' => 'facility-wc-south-east-m', 'type' => 'bathroom'],
        ['number' => 'WC-SOUTH-EAST-F', 'gender' => 'female', 'svg_id' => 'facility-wc-south-east-f', 'type' => 'bathroom'],
        ['number' => 'MOSQUE-01', 'gender' => 'unisex', 'svg_id' => 'facility-mosque-01', 'type' => 'mosque'],
        ['number' => 'PARKING-NORTH-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-parking-north-east', 'type' => 'parking'],
        ['number' => 'PARKING-MIDDLE-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-parking-middle-east', 'type' => 'parking'],
        ['number' => 'PARKING-SOUTH-WEST', 'gender' => 'unisex', 'svg_id' => 'facility-parking-south-west', 'type' => 'parking'],
        ['number' => 'PARKING-SOUTH-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-parking-south-east', 'type' => 'parking'],
        ['number' => 'HVAC-01', 'gender' => 'unisex', 'svg_id' => 'facility-hvac-01', 'type' => 'hvac'],
        ['number' => 'PRESS-01', 'gender' => 'unisex', 'svg_id' => 'facility-press-01', 'type' => 'press'],
        ['number' => 'VIP-LOUNGE-01', 'gender' => 'unisex', 'svg_id' => 'facility-vip-lounge-01', 'type' => 'vip_lounge'],
        ['number' => 'MAIN-ENTRANCE-EXIT', 'gender' => 'unisex', 'svg_id' => 'facility-main-entrance-exit', 'type' => 'entrance_exit'],
        ['number' => 'GOODS-ENTRANCE-WEST', 'gender' => 'unisex', 'svg_id' => 'facility-goods-entrance-west', 'type' => 'goods_entrance'],
        ['number' => 'GOODS-ENTRANCE-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-goods-entrance-east', 'type' => 'goods_entrance'],
        ['number' => 'EMERGENCY-EXIT-WEST', 'gender' => 'unisex', 'svg_id' => 'facility-emergency-exit-west', 'type' => 'emergency_exit'],
        ['number' => 'EMERGENCY-EXIT-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-emergency-exit-east', 'type' => 'emergency_exit'],
        ['number' => 'ENTRANCE-WEST', 'gender' => 'unisex', 'svg_id' => 'facility-entrance-west', 'type' => 'entrance'],
        ['number' => 'ENTRANCE-EAST', 'gender' => 'unisex', 'svg_id' => 'facility-entrance-east', 'type' => 'entrance'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::FACILITIES as $facilityData) {
            Facility::query()->updateOrCreate(
                ['number' => $facilityData['number']],
                $facilityData,
            );
        }
    }
}
