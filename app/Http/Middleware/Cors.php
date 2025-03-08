<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        $response = $next($request);

        // Agregar encabezados CORS
        $response->headers->set("Access-Control-Allow-Origin", "*");
        $response->headers->set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
        $response->headers->set("Access-Control-Allow-Headers", "Authorization, Content-Type, X-Requested-With");

        // Manejo de pre-flight requests (OPCIONAL)
        if ($request->isMethod("OPTIONS")) {
            return response()->json("OK", 200, $response->headers->all());
        }

        return $response;
    }
}
