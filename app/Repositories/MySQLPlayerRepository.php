<?php


namespace App\Repositories;

use App\Models\Player;
use App\Repositories\Contracts\PlayerRepositoryInterface;

class MySQLPlayerRepository implements PlayerRepositoryInterface
{
    public function __construct(protected Player $model){}

    /**
     * @inheritDoc
     */
    public function create(string $username): Player {
        return $this->model->query()
            ->create(['name' => $username, 'total_score' => 0]);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?Player {
        return $this->model->query()->find($id);
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $username): ?Player {
        return $this->model->query()
            ->where('name', $username)
            ->first();
    }


    /**
     * @inheritDoc
     */
    public function updateScore(int $playerId, int $newTotalScore): void {
        $this->model->query()
            ->where('id', $playerId)
            ->update(['total_score' => $newTotalScore]);
    }


    /**
     * @inheritDoc
     */
    public function topN(int $limit): array {
        return $this->model->query()
            ->orderByDesc('total_score')
            ->limit($limit)
            ->get(['id','name','total_score'])
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function count():int
    {
        return $this->model->query()->count();
    }

    /**
     * @inheritDoc
     */
    public function firstOrCreate(string $name):Player
    {
       return $this->model->query()
            ->firstOrCreate(['name'=>$name], ['total_score'=>0]);
    }

    /**
     * @inheritDoc
     */
    public function getIdsInArray($players): array
    {
        return $this->model->query()
            ->inRandomOrder()
            ->limit($players)
            ->pluck('id')
            ->toArray();
    }
}
