<?php

namespace Qsque\FilamentTranslationHelper\Resources;

use Qsque\FilamentTranslationHelper\Support\TranslationHelper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Str;

use function Filament\Support\locale_has_pluralization;

abstract class BaseResource extends Resource
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $hasTitleCaseModelLabel = false;

    public static function getModelLabel(): string
    {
        return static::getTranslatedLabel('label');
    }

    public static function getPluralModelLabel(): string
    {
        $resourceKey = static::getResourceKey();
        $translationKey = "resources.{$resourceKey}.plural_label";

        if (trans()->has($translationKey)) {
            return __($translationKey);
        }

        if (locale_has_pluralization()) {
            return Str::plural(static::getModelLabel());
        }

        return static::getModelLabel();
    }

    protected static function getTranslatedLabel(string $key): string
    {
        $resourceKey = static::getResourceKey();
        $translationKey = "resources.{$resourceKey}.{$key}";

        return TranslationHelper::getWithFallback($translationKey, static::hasTitleCaseModelLabel());
    }

    public static function form(Schema $schema): Schema
    {
        $formClass = static::getFormClass();

        if (class_exists($formClass) && method_exists($formClass, 'configure')) {
            return $formClass::configure($schema);
        }

        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        $tableClass = static::getTableClass();

        if (class_exists($tableClass) && method_exists($tableClass, 'configure')) {
            return $tableClass::configure($table);
        }

        return $table;
    }

    protected static function getFormClass(): string
    {
        $resourceNamespace = static::class;
        $resourceName = class_basename($resourceNamespace);
        $baseNamespace = str_replace('\\' . $resourceName, '', $resourceNamespace);
        $entityName = Str::replaceLast('Resource', '', $resourceName);

        return "{$baseNamespace}\\Schemas\\{$entityName}Form";
    }

    protected static function getTableClass(): string
    {
        $resourceNamespace = static::class;
        $resourceName = class_basename($resourceNamespace);
        $baseNamespace = str_replace('\\' . $resourceName, '', $resourceNamespace);
        $entityName = Str::replaceLast('Resource', '', $resourceName);

        return "{$baseNamespace}\\Tables\\{$entityName}sTable";
    }

    protected static function getResourceKey(): string
    {
        $className = class_basename(static::class);
        $withoutResource = Str::replaceLast('Resource', '', $className);

        return Str::snake($withoutResource, '-');
    }

    public static function getPages(): array
    {
        $baseNamespace = static::getPagesNamespace();
        $entityName = static::getEntityName();

        $pages = [];

        $listClass = "{$baseNamespace}\\List{$entityName}s";
        if (class_exists($listClass)) {
            $pages['index'] = $listClass::route('/');
        }

        $createClass = "{$baseNamespace}\\Create{$entityName}";
        if (class_exists($createClass)) {
            $pages['create'] = $createClass::route('/create');
        }

        $editClass = "{$baseNamespace}\\Edit{$entityName}";
        if (class_exists($editClass)) {
            $pages['edit'] = $editClass::route('/{record}/edit');
        }

        return $pages;
    }

    protected static function getPagesNamespace(): string
    {
        $resourceNamespace = static::class;
        $resourceName = class_basename($resourceNamespace);
        $baseNamespace = str_replace('\\' . $resourceName, '', $resourceNamespace);

        return "{$baseNamespace}\\Pages";
    }

    protected static function getEntityName(): string
    {
        $resourceName = class_basename(static::class);
        return Str::replaceLast('Resource', '', $resourceName);
    }
}