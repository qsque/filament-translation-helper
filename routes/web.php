<?php

use Qsque\FilamentTranslationHelper\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::prefix('locale')->group(function () {
    Route::get('/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
});