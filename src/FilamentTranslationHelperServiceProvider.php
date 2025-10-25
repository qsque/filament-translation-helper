<?php

namespace Qsque\FilamentTranslationHelper;

use Qsque\FilamentTranslationHelper\Providers\FilamentMacroServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslationHelperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-translation-helper')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->register(FilamentMacroServiceProvider::class);
    }

    public function packageBooted(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'filament-translation-helper');
        
        $this->publishes([
            __DIR__ . '/../config/filament-translation-helper.php' => config_path('filament-translation-helper.php'),
        ], 'filament-translation-helper-config');

        $this->publishes([
            __DIR__ . '/../lang' => lang_path('vendor/filament-translation-helper'),
        ], 'filament-translation-helper-lang');
    }
}