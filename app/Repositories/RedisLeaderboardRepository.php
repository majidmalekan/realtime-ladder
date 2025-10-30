<?php

namespace App\Repositories;

use App\Repositories\Contracts\LeaderboardRepositoryInterface;
use Illuminate\Support\Facades\Redis;

class RedisLeaderboardRepository implements LeaderboardRepositoryInterface
{
    private string $key = 'leaderboard';

    /**
     * @inheritDoc
     */
    public function incrBy(int $playerId, int $delta): int
    {
        $new = Redis::zincrby($this->key, $delta, (string)$playerId);
        return (int)$new;
    }


    /**
     * @inheritDoc
     */
    public function setScore(int $playerId, int $score): void
    {
        Redis::zadd($this->key, ['NX' => false, 'CH' => true], $score, (string)$playerId);
    }

    /**
     * @inheritDoc
     */
    public function getScore(int $playerId): ?int
    {
        $s = Redis::zscore($this->key, (string)$playerId);
        return is_null($s) ? null : (int)$s;
    }

    /**
     * @inheritDoc
     */
    public function rank(int $playerId): ?int
    {
        $r = Redis::zrevrank($this->key, (string)$playerId);
        return $r === null ? null : (int)$r;
    }

    /**
     * @inheritDoc
     */
    public function topN(int $limit): array
    {
        $res = Redis::zrevrange($this->key, 0, $limit - 1, ['withscores' => true]);
        $out = [];
        foreach ($res as $member => $score) {
            $out[] = [(int)$member, (int)$score];
        }
        return $out;
    }

    /**
     * @inheritDoc
     */
    public function remove(int $playerId): void
    {
        Redis::zrem($this->key, (string)$playerId);
    }
}
