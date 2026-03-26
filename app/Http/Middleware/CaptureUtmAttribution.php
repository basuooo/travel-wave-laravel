<?php

namespace App\Http\Middleware;

use App\Support\UtmAttributionService;
use Closure;
use Illuminate\Http\Request;

class CaptureUtmAttribution
{
    public function __construct(
        protected UtmAttributionService $utmAttributionService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (
            ! $request->isMethod('GET')
            || $request->is('admin')
            || $request->is('admin/*')
            || $request->is('locale/*')
            || $request->expectsJson()
        ) {
            return $next($request);
        }

        $this->utmAttributionService->captureFromRequest($request);

        return $next($request);
    }
}
