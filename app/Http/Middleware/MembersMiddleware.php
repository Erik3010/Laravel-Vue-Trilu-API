<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;
use App\BoardMember;

class MembersMiddleware
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

        $boardId = $request->route()->parameters()['boardId'];

        $memberCheck = BoardMember::where([
            'board_id' => $boardId,
            'user_id' => $userId
        ])->first();

        if(!$memberCheck) {
            return response()->json(['message' => 'unauthorized user'], 200);
        }

        return $next($request);
    }
}
