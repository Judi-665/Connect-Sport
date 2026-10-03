<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSupporterAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isSupporter()) {
            abort(403, 'Cette fonctionnalité n’est pas accessible aux supporters.');
        }

        return $next($request);
    }
}