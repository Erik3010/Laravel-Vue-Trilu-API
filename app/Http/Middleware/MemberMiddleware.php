<?php

namespace App\Http\Middleware;

use Closure;

use App\Token;
use App\BoardMember;

class MemberMiddleware
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

        $boardId = $request->board;

        // $param = $request->route()->parameters('boardId');
        // $param = $request->route()->parameters();

        $checkBoardMember = BoardMember::where([
            'board_id' => $boardId->id,
            'user_id' => $userId
        ])->first();

        if(!$checkBoardMember) {
            return response()->json(['message' => 'unauthorized user'], 401);
        }

        return $next($request);
    }
}
