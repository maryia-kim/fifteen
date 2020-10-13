<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\Board\CreateBoardService;
use App\Services\Game\SolveGameService;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * Request body example: {"board":"1,2,3,0,5,6,7,8,9,10,11,12,13,14,15,4"}
     *
     * @param CreateGameRequest $request
     * @return GameResource
     */
    public function store(CreateGameRequest $request)
    {
        $gameBoard = (new CreateBoardService())->get($request->validated());

        $game = Game::create([
            'user_id' => Auth::id(),
            'board' => $gameBoard,
        ]);

        return GameResource::make($game);
    }


    /**
     * Check solution
     *
     * @param \App\Models\Game $game
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function solve(Game $game, Request $request)
    {
        $response = Gate::inspect('solve', $game);

        if ($response->allowed()) {
            $solutionRequestTime = Carbon::now();
            $solutionService = new SolveGameService($game, $request->input('steps'));

            //проверка, что игра еще не решена
            if ($game->solved_at != null) {
                $solvedAt = $solutionService->getTime($game->solved_at);
                return response()->json(['message' => 'Game already solved! Your time:' . $solvedAt]);
            }

            $isGameSolved = $solutionService->solve();

            if ($isGameSolved === false) {
                return response()->json('Wrong solution, try again');
            }

            $game->update(['solved_at' => $solutionRequestTime]);
            $solvedAt = $solutionService->getTime($solutionRequestTime);
            return response()->json(['message' => 'Game solved! Your time:' . $solvedAt]);
        }

        return response()->json($response);
    }
}
