<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionOutcome;

class InteractionOutcomeSeeder extends Seeder
{
    public function run(): void
    {
        InteractionOutcome::factory()
            ->count(10)
            ->create();
    }
}