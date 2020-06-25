<?php

namespace App\Http\Middleware;

use Closure;

use App\Token;
use App\Card;
use App\BoardList;
use App\BoardMember;

class CardMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->token;
        $userId = Token::where('token', $token)->first()->user_id;


        $cardId = $request->route()->parameters()['cardId'];
        $listId = Card::where(['id' => $cardId])->first()->list_id;
        $cardBoard = BoardList::where('id', $listId)->first()->board_id;

        $check = BoardMember::where([
            'board_id' => $cardBoard,
            'user_id' => $userId
        ])->first();

        if(!$check) {
            return response()->json(['message' => 'unauhtorized user', $listId], 401);
        }

        return $next($request);
    }
}
