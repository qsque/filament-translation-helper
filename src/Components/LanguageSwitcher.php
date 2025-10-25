<?php

namespace Qsque\FilamentTranslationHelper\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;

class LanguageSwitcher
{
    public static function getUserMenuItem(): Action
    {
        $availableLocales = config('filament-translation-helper.available_locales', ['en' => 'English']);
        $currentLocale = app()->getLocale();
        $currentLabel = $availableLocales[$currentLocale] ?? $currentLocale;

        return Action::make('language-switcher')
            ->label($currentLabel)
            ->icon('hero"""icon-o-language')
            ->schema([
                Radio::make('locale')
                    ->options($availableLocales)
                    ->default(fn () => app()->getLocale())
                    ->inline(false)
                    ->hiddenLabel(),
            ])
            ->modalHeading('')
            ->action(function (array $data) {
                if (isset($data['locale']) && in_array($data['locale'], array_keys(config('filament-translation-helper.available_locales', ['en' => 'English'])))) {
                    return redirect()->to(route('locale.switch', $data['locale']));
                }

                return redirect()->to(route('locale.switch', config('app.locale')));
            })
            ->modalSubmitActionLabel()
            ->modalCancelActionLabel();
    }
}