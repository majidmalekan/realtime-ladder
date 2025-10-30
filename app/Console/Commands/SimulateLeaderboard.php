<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Services\LeaderboardService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Random\RandomException;

class SimulateLeaderboard extends Command
{
    protected $signature = 'leaderboard:simulate {players=1000} {updates=500}';
    protected $description = 'Simulate random score updates (positive/negative)';

    /**
     * @param LeaderboardService $svc
     * @return int
     * @throws BindingResolutionException
     * @throws RandomException
     */
    public function handle(LeaderboardService $svc): int
    {
        $players = (int) $this->argument('players');
        $updates = (int) $this->argument('updates');
        $playerRepository=app()->make(PlayerRepositoryInterface::class);
        if ($playerRepository->count() < $players) {
            for ($i=1; $i<=$players; $i++) {
                $name = "user_$i";
                $playerRepository->firstOrCreate($name);
            }
        }
        $this->call('leaderboard:rebuild');

        $ids = $playerRepository->getIdsInArray($players);
        $this->info("Updating {$updates} random deltas...");
        $bar = $this->output->createProgressBar($updates);

        for ($i=0; $i<$updates; $i++) {
            $id = $ids[array_rand($ids)];
            try {
                $delta = random_int(-50, 100);
            } catch (RandomException $e) {
                throw new RandomException("Random number generation failed");
            }
            $svc->updateScoreDelta($id, $delta);
            $bar->advance();
        }
        $bar->finish(); $this->newLine();

        $top = $svc->getTopN(20);
        $this->table(['#','player_id','name','score'], array_map(function($row, $i){
            return [$i+1, $row['player_id'], $row['name'], $row['score']];
        }, $top, array_keys($top)));

        return self::SUCCESS;
    }
}
