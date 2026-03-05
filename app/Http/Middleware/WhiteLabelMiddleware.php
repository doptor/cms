<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * WhiteLabel Middleware
 *
 * Allows agencies to rebrand RyaanCMS with their own:
 * - Logo & brand name
 * - Colors & theme
 * - Custom domain
 * - Remove "Powered by RyaanCMS" footer
 *
 * Configure per-tenant in: config/whitelabel.php
 * Or per-domain in: database table `whitelabel_configs`
 */
class WhiteLabelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $config = $this->resolveConfig($request->getHost());

        if ($config) {
            // Share white-label config with all views
            view()->share('whitelabel', $config);
            view()->share('app_name',   $config['name']  ?? config('app.name'));
            view()->share('app_logo',   $config['logo']  ?? null);
            view()->share('app_color',  $config['color'] ?? '#6c63ff');
            view()->share('hide_branding', $config['hide_branding'] ?? false);
        }

        return $next($request);
    }

    private function resolveConfig(string $domain): ?array
    {
        // Check database for domain-specific white-label config
        try {
            $wl = \DB::table('whitelabel_configs')
                ->where('domain', $domain)
                ->where('is_active', true)
                ->first();

            if ($wl) {
                return json_decode($wl->config, true);
            }
        } catch (\Exception) {}

        // Fall back to config file
        return config('whitelabel.default');
    }
}
