<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Config\Auth;
use Closure;
use Config;

class BasicAuthenticate
{
    public function handle(Request $request, Closure $next, $api_token)
    {
        $users = Config::get('baseauth.users');
        if($request->get('api_token') != $users[0][1]){
            return response()->json(['message' => 'Api token invalid','status' => 404],404);
        }
        return $next($request);
    }
}