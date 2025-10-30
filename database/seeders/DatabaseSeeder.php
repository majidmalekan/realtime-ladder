<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $count = (int) ($this->command?->option('count') ?? config('constants.PLAYER_SEED_COUNT', 1000));
        $this->callWith(PlayerSeeder::class, ['count' => $count]);
        $this->command->info("🎯 Seeding completed with {$count} players.");

    }
}
