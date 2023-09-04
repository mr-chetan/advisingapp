<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\ServiceRequestPriority;

class CaseItemPrioritySeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestPriority::factory()
            ->count(3)
            ->sequence(
                ['name' => 'High', 'order' => 1],
                ['name' => 'Medium', 'order' => 2],
                ['name' => 'Low', 'order' => 3],
            )
            ->create();
    }
}
