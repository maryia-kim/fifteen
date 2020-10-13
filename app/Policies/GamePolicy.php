<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    use HandlesAuthorization;


    /**
     * Determine whether the user can solve the game.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Game $game
     * @return mixed
     */
    public function solve(User $user, Game $game)
    {
        return $user->id === $game->user_id
            ? Response::allow()
            : Response::deny('Wrong game!', 403);
    }

}
