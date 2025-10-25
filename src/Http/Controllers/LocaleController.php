<?php

namespace Qsque\FilamentTranslationHelper\Http\Controllers;

use Illuminate\Routing\Controller;

class LocaleController extends Controller
{
    public function switch(string $locale)
    {
        if (in_array($locale, array_keys(config('filament-translation-helper.available_locales', ['en' => 'English'])))) {
            $cookie = cookie('locale', $locale, 525600);
            session(['locale' => $locale]);

            return redirect()->back()->withCookie($cookie);
        }

        return redirect()->back();
    }
}
