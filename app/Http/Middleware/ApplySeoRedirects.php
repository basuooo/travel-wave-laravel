<?php

namespace App\Http\Middleware;

use App\Models\SeoRedirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ApplySeoRedirects
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Schema::hasTable('seo_redirects')) {
                $path = '/' . ltrim($request->path(), '/');

                $redirect = SeoRedirect::query()
                    ->where('source_path', $path)
                    ->where('is_active', true)
                    ->first();

                if ($redirect) {
                    $redirect->increment('hit_count');
                    $redirect->forceFill(['last_hit_at' => now()])->save();

                    return redirect($redirect->destination_url, $redirect->redirect_type);
                }
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return $next($request);
    }
}
