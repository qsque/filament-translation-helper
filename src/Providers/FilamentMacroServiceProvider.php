<?php

namespace Qsque\FilamentTranslationHelper\Providers;

use Qsque\FilamentTranslationHelper\Support\TranslationHelper;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\Column;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class FilamentMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->interceptFieldCreation();
        $this->interceptSectionCreation();
        $this->interceptColumnCreation();
    }

    protected function interceptFieldCreation(): void
    {
        Field::configureUsing(function (Field $field) {
            $fieldName = $field->getName();
            $resourceKey = $this->getResourceKeyFromContext();

            if ($resourceKey && $fieldName) {
                $translation = $this->findTranslation($resourceKey, $fieldName, ['fields', 'form.fields']);
                if ($translation) {
                    $field->label($translation);
                }
            }
        });
    }

    protected function getResourceKeyFromContext(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && (str_ends_with($frame['class'], 'Form') || str_ends_with($frame['class'], 'Table'))) {
                $parts = explode('\\', $frame['class']);
                if (count($parts) >= 4 && $parts[0] === 'App' && $parts[1] === 'Filament' && $parts[2] === 'Resources') {
                    $resourceName = $parts[3];
                    return Str::snake(Str::replaceLast('s', '', $resourceName), '-');
                }
            }
        }

        return null;
    }

    protected function interceptSectionCreation(): void
    {
        Section::configureUsing(function (Section $section) {
            $heading = $section->getHeading();
            
            if ($heading) {
                $resourceKey = $this->getResourceKeyFromContext();
                if ($resourceKey) {
                    $translation = $this->findTranslation($resourceKey, $heading, ['sections', 'form.sections']);
                    if ($translation) {
                        $section->heading($translation);
                    } else {
                        $fallback = TranslationHelper::getWithFallback("sections.{$heading}");
                        $section->heading($fallback);
                    }
                }
            }
        });
    }

    protected function interceptColumnCreation(): void
    {
        Column::configureUsing(function (Column $column) {
            $columnName = $column->getName();
            $resourceKey = $this->getResourceKeyFromContext();

            if ($resourceKey && $columnName) {
                $translation = $this->findTranslation($resourceKey, $columnName, ['columns', 'table.columns']);
                if ($translation) {
                    $column->label($translation);
                }
            }
        });
    }

    protected function findTranslation(string $resourceKey, string $name, array $pathPrefixes): ?string
    {
        foreach ($pathPrefixes as $prefix) {
            $translationKey = "resources.{$resourceKey}.{$prefix}.{$name}";
            if (trans()->has($translationKey)) {
                return __($translationKey);
            }
        }

        return $this->getCommonFieldTranslation($name);
    }

    protected function getCommonFieldTranslation(string $fieldName): ?string
    {
        $commonTranslationKey = "common.fields.{$fieldName}";
        if (trans()->has($commonTranslationKey)) {
            return __($commonTranslationKey);
        }

        $packageTranslationKey = "filament-translation-helper::common.fields.{$fieldName}";
        if (trans()->has($packageTranslationKey)) {
            return __($packageTranslationKey);
        }

        $baseName = explode('.', $fieldName)[0];
        $baseTranslationKey = "common.fields.{$baseName}";
        if (trans()->has($baseTranslationKey)) {
            return __($baseTranslationKey);
        }

        $packageBaseTranslationKey = "filament-translation-helper::common.fields.{$baseName}";
        if (trans()->has($packageBaseTranslationKey)) {
            return __($packageBaseTranslationKey);
        }

        return null;
    }
}