<?php

namespace App\Services;

use App\Models\Player;
use App\Repositories\Contracts\LeaderboardRepositoryInterface;
use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Jobs\SyncPlayerScoreJob;
use Illuminate\Support\Facades\DB;

readonly class LeaderboardService
{
    public function __construct(
        private PlayerRepositoryInterface      $players,
        private LeaderboardRepositoryInterface $board
    ) {}

    /**
     * @param string $username
     * @return Player
     * @throws \Throwable
     */
    public function createPlayer(string $username): Player {
        return DB::transaction(function () use ($username) {
            $player = $this->players->create($username);
            $this->board->setScore($player->id, 0);
            return $player;
        });
    }

    /**
     * @param int $playerId
     * @param int $delta
     * @return array{player_id: int, score: int, rank: int|null}
     */
    public function updateScoreDelta(int $playerId, int $delta): array {
        $newScore = $this->board->incrBy($playerId, $delta);
        SyncPlayerScoreJob::dispatch($playerId);
        $rank = $this->board->rank($playerId);
        return ['player_id' => $playerId, 'score' => $newScore, 'rank' => $rank === null ? null : $rank + 1];
    }

    /**
     * @param int $playerId
     * @param int $score
     * @return array{player_id: int, score: int, rank: int|null}
     */
    public function setAbsoluteScore(int $playerId, int $score): array {
        $this->board->setScore($playerId, $score);
        SyncPlayerScoreJob::dispatch($playerId);
        $rank = $this->board->rank($playerId);
        return ['player_id' => $playerId, 'score' => $score, 'rank' => $rank === null ? null : $rank + 1];
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopN(int $limit): array {
        $rows = $this->board->topN($limit);
        $out = [];
        if (!$rows) return $out;

        $ids = array_map(fn($r) => $r[0], $rows);
        $players = Player::query()->whereIn('id', $ids)->get(['id','name'])->keyBy('id');
        foreach ($rows as [$id, $score]) {
            $out[] = [
                'player_id' => $id,
                'name'      => $players[$id]->name ?? null,
                'score'     => $score,
            ];
        }
        return $out;
    }

    /**
     * @param int $playerId
     * @return array|null
     */
    public function getPlayerRank(int $playerId): ?array {
        $rank = $this->board->rank($playerId);
        if ($rank === null) return null;
        $score = $this->board->getScore($playerId) ?? 0;
        $player = $this->players->findById($playerId);
        return [
            'player_id' => $playerId,
            'name'      => $player?->name,
            'score'     => $score,
            'rank'      => $rank + 1,
        ];
    }
}
