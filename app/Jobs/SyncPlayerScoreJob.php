<?php

namespace App\Jobs;

use App\Repositories\Contracts\LeaderboardRepositoryInterface;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncPlayerScoreJob implements ShouldQueue
{
    use Queueable;

    public int $playerId;

    public function __construct(int $playerId) { $this->playerId = $playerId; }

    public function handle(PlayerRepositoryInterface $players, LeaderboardRepositoryInterface $board): void
    {
        $score = $board->getScore($this->playerId);
        if ($score !== null) {
            $players->updateScore($this->playerId, $score);
        }
    }
}
