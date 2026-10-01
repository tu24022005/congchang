<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceUtf8
{
    /**
     * Chuẩn hóa charset cho HTML response để tiếng Việt không bị giải mã sai.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $contentType = $response->headers->get('Content-Type', '');
        if (str_contains(strtolower($contentType), 'text/html')) {
            $response->headers->set('Content-Type', 'text/html; charset=utf-8');
        }

        return $response;
    }
}
