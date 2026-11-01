<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appUrl = (string) config('app.url');

        if (str_starts_with($appUrl, 'https://') || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Prefer ASSET_URL when set (public tunnel / CDN).
        if ($assetUrl = config('app.asset_url')) {
            URL::forceRootUrl(rtrim((string) config('app.url'), '/'));
        }
    }
}
