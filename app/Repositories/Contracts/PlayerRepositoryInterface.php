<?php
namespace App\Repositories\Contracts;

use App\Models\Player;

interface PlayerRepositoryInterface {
    /**
     * @param string $username
     * @return Player
     */
    public function create(string $username): Player;

    /**
     * @param int $id
     * @return Player|null
     */
    public function findById(int $id): ?Player;

    /**
     * @param string $username
     * @return Player|null
     */
    public function findByName(string $username): ?Player;

    /**
     * @param int $playerId
     * @param int $newTotalScore
     * @return void
     */
    public function updateScore(int $playerId, int $newTotalScore): void;

    /**
     * @param int $limit
     * @return array
     */
    public function topN(int $limit): array;

    /**
     * @return int
     */
    public function count():int;

    /**
     * @param string $name
     * @return Player
     */
    public function firstOrCreate(string $name):Player;

    /**
     * @param $players
     * @return array
     */
    public function getIdsInArray($players):array;
}
