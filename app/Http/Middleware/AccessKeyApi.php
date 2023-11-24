<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessKeyApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->headers->get('x_api_key');
        if ($apiKey !== env('X_API_KEY')) {
            return response()->json(['mensage' => 'No autorizado']);
        } else {
        }

        return $next($request);
    }
}
