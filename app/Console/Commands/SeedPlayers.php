<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\PlayerSeeder;

class SeedPlayers extends Command
{
    protected $signature = 'players:seed {count=1000} {--rebuild}';
    protected $description = 'Seed fake players (count) and optionally rebuild leaderboard';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        (new PlayerSeeder)->setContainer(app())->setCommand($this)->run($count);
        $this->info("✅ Seeded {$count} players.");
        if ($this->option('rebuild')) {
            $this->call('leaderboard:rebuild');
        }
        return self::SUCCESS;
    }
}
