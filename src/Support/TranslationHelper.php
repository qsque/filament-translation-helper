<?php

namespace Qsque\FilamentTranslationHelper\Support;

use Illuminate\Support\Str;

class TranslationHelper
{
    public static function getWithFallback(string $translationKey, bool $titleCase = true): string
    {
        if (trans()->has($translationKey)) {
            return __($translationKey);
        }

        $parts = explode('.', $translationKey);
        
        if (count($parts) >= 3 && $parts[0] === 'resources' && in_array($parts[2], ['label', 'plural_label'])) {
            $fallbackName = $parts[1];
        } else {
            $fallbackName = collect($parts)->last();
        }

        $result = str_replace(['-', '_'], ' ', $fallbackName);
        
        return $titleCase ? Str::title($result) : Str::ucfirst($result);
    }
}