<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;

class AuthMiddleware
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
        if($token) {
            $isToken = Token::where('token', $token)->first();

            if(!$isToken) {
                return response()->json(['message' => 'unauthorized user'], 401);
            }

            return $next($request);

        }
        return response()->json(['message' => 'unauthorized user'], 401);
        // return $next($request);
    }
}
