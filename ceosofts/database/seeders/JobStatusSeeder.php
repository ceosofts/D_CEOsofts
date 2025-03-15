<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobStatus;

class JobStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'color' => '#FFA500', 'sort_order' => 1],
            ['name' => 'In Progress', 'color' => '#0000FF', 'sort_order' => 2],
            ['name' => 'Completed', 'color' => '#008000', 'sort_order' => 3],
            ['name' => 'Cancelled', 'color' => '#FF0000', 'sort_order' => 4],
        ];

        foreach ($statuses as $status) {
            JobStatus::create($status);
        }
    }
}
