<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;
use App\Board;

class CreatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $board)
    {
        $token = $request->token;
        $userId = Token::where('token', $token)->first()->user_id;

        // $boardId = $request->board;
        $boardId = $request->$board;

        $checkCreator = Board::where([
            'id' => $boardId,
            'creator_id' => $userId
        ])->first();

        if(!$checkCreator) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        return $next($request);
    }
}
