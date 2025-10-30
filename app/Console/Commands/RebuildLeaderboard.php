<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Repositories\Contracts\LeaderboardRepositoryInterface;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RebuildLeaderboard extends Command
{
    protected $signature = 'leaderboard:rebuild';
    protected $description = 'Rebuild Redis ZSET from MySQL';

    public function handle(LeaderboardRepositoryInterface $board): int
    {

        Redis::del('leaderboard');

        $bar = $this->output->createProgressBar(app()->make(PlayerRepositoryInterface::class)->count());
        Player::query()->chunk(1000, function ($chunk) use ($board, $bar) {
            Redis::pipeline(function ($pipe) use ($chunk) {
                foreach ($chunk as $p) {
                    $pipe->zadd('leaderboard', $p->total_score, (string)$p->id);
                }
            });
            $bar->advance(count($chunk));
        });
        $bar->finish();
        $this->newLine();
        $this->info('Rebuild completed.');
        return self::SUCCESS;
    }
}
