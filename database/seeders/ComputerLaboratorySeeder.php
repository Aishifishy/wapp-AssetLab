<?php

namespace Database\Seeders;

use App\Models\ComputerLaboratory;
use Illuminate\Database\Seeder;

class ComputerLaboratorySeeder extends Seeder
{
    public function run(): void
    {
        $laboratories = [
            [
                'name' => 'Computer Laboratory 1',
                'room_number' => 'CL-101',
                'building' => 'IT Building',
                'capacity' => 40,
                'number_of_computers' => 40,
                'equipment_inventory' => [
                    'Projector' => 1,
                    'Whiteboard' => 1,
                    'Air Conditioning' => 2,
                    'Teacher\'s Computer' => 1,
                ],
                'status' => ComputerLaboratory::STATUS_AVAILABLE,
            ],
            [
                'name' => 'Computer Laboratory 2',
                'room_number' => 'CL-102',
                'building' => 'IT Building',
                'capacity' => 40,
                'number_of_computers' => 40,
                'equipment_inventory' => [
                    'Projector' => 1,
                    'Whiteboard' => 1,
                    'Air Conditioning' => 2,
                    'Teacher\'s Computer' => 1,
                ],
                'status' => ComputerLaboratory::STATUS_AVAILABLE,
            ],
            [
                'name' => 'Computer Laboratory 3',
                'room_number' => 'CL-201',
                'building' => 'IT Building',
                'capacity' => 30,
                'number_of_computers' => 30,
                'equipment_inventory' => [
                    'Projector' => 1,
                    'Whiteboard' => 1,
                    'Air Conditioning' => 2,
                    'Teacher\'s Computer' => 1,
                ],
                'status' => ComputerLaboratory::STATUS_AVAILABLE,
            ],
        ];

        foreach ($laboratories as $lab) {
            ComputerLaboratory::create($lab);
        }
    }
} 