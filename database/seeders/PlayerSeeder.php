<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 1000): void
    {

        DB::table('players')->truncate();

        $this->command->info("Creating {$count} players...");
        Player::factory()->count($count)->create();
        $this->command->info("✅ Done. {$count} players created successfully.");
    }
}
