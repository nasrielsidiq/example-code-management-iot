<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LampControl;
use App\Models\Device;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 3 lamps
        LampControl::create(['name' => 'Living Room Lamp']);
        LampControl::create(['name' => 'Kitchen Lamp']);
        LampControl::create(['name' => 'Bedroom Lamp']);

        // Create sample device
        Device::create([
            'device_id' => 'ESP32_001',
            'api_key' => 'sample_api_key_123',
            'name' => 'Temperature Sensor 1'
        ]);
    }
}