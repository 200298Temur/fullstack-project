<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        if($request->path()=='app/admin_login'){
            return $next($request);
        }
        if(!Auth::check()){
            // return redirect('/');
            return response()->json([
                'msg'=>'You are note allowed to accsess this route..',
                'url'=>$request->path()
            ],403);
        }
        $user=Auth::user();
        if($user->role->isAdmin==0){
            return response()->json([
                'msg'=>"You are note allowed to accsess this route.."
            ],403);
        }
        return $next($request);
    }
}
