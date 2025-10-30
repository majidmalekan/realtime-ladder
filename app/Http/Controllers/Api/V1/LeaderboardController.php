<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Player\CreatePlayerRequest;
use App\Http\Requests\Player\UpdateScoreRequest;
use App\Models\Player;
use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(private readonly LeaderboardService $service) {}

    /**
     * @throws \Throwable
     */
    public function store(CreatePlayerRequest $request): JsonResponse {
        $p = $this->service->createPlayer($request->string('name'));
        return response()->json(['id'=>$p->id,'name'=>$p->name,'total_score'=>$p->total_score], 201);
    }

    public function updateScore(UpdateScoreRequest $request, Player $player): JsonResponse {
        $mode  = $request->string('mode');
        $value = $request->integer('value');

        $res = $mode == 'delta'
            ? $this->service->updateScoreDelta($player->id, $value)
            : $this->service->setAbsoluteScore($player->id, $value);

        return response()->json($res);
    }

    public function top(): JsonResponse {
        $limit = (int) request('limit', 10);
        $limit = max(1, min($limit, 1000));
        return response()->json($this->service->getTopN($limit));
    }

    public function rank(Player $player): JsonResponse {
        $res = $this->service->getPlayerRank($player->id);
        return $res ? response()->json($res) : response()->json(['message'=>'Not found in leaderboard'], 404);
    }
}
