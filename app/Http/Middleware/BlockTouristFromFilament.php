<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockTouristFromFilament
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
    
        if ($user && ($user->hasRole('Tourist') || $user->roles->isEmpty())) {
            abort(403, 'Unauthorized');
        }
    
        return $next($request);
    }
    
}
