<?php

namespace Qsque\FilamentTranslationHelper\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale')
            ?? session('locale')
            ?? config('filament-translation-helper.default_locale', config('app.locale'));

        if (in_array($locale, array_keys(config('filament-translation-helper.available_locales', ['en' => 'English'])))) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
