<?php
namespace App\Repositories\Contracts;

interface LeaderboardRepositoryInterface {
    /**
     * @param int $playerId
     * @param int $delta
     * @return int
     */
    public function incrBy(int $playerId, int $delta): int; // returns new score

    /**
     * @param int $playerId
     * @param int $score
     * @return void
     */
    public function setScore(int $playerId, int $score): void;

    /**
     * @param int $playerId
     * @return int|null
     */
    public function getScore(int $playerId): ?int;

    /**
     * @param int $playerId
     * @return int|null
     */
    public function rank(int $playerId): ?int; // 0-based in Redis, we’ll +1 in service

    /**
     * @param int $limit
     * @return array
     */
    public function topN(int $limit): array;   // [ [playerId, score], ... ]

    /**
     * @param int $playerId
     * @return void
     */
    public function remove(int $playerId): void;
}
