<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckWebsiteStatus
{
    /**
     * Handle an incoming request for the public frontend.
     */
    public function handle(Request $request, Closure $next)
    {
        $setting = Setting::query()->first();

        if (!$setting) {
            return $next($request);
        }

        $status = $setting->site_status ?? 'active';

        // 1. Redirect Mode
        if ($status === 'redirect' && !empty($setting->site_redirect_url)) {
            $redirectUrl = $setting->site_redirect_url;
            if (!str_starts_with($redirectUrl, 'http://') && !str_starts_with($redirectUrl, 'https://')) {
                $redirectUrl = 'https://' . $redirectUrl;
            }
            return redirect()->away($redirectUrl);
        }

        // 2. Maintenance Mode
        if ($status === 'maintenance') {
            // Check if logged-in admin should bypass maintenance page
            $bypassAdmin = (bool) ($setting->maintenance_bypass_admin ?? true);
            if ($bypassAdmin && Auth::check()) {
                $user = Auth::user();
                if ($user->is_admin || (method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
                    return $next($request);
                }
            }

            return response()->view('frontend.maintenance', [
                'setting' => $setting,
            ], 503);
        }

        // 3. Active Mode
        return $next($request);
    }
}
