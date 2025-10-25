<?php

namespace Qsque\FilamentTranslationHelper\Tests\Unit;

use Qsque\FilamentTranslationHelper\Resources\BaseResource;
use Qsque\FilamentTranslationHelper\Tests\TestCase;

class BaseResourceTest extends TestCase
{
    public function test_returns_translation_for_model_label_when_available()
    {
        app('translator')->addLines([
            'resources.test-resource.label' => 'Тестовый ресурс'
        ], 'ru');

        app()->setLocale('ru');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            public static function getResourceKey(): string { return 'test-resource'; }
        };

        $this->assertEquals('Тестовый ресурс', $resource::getModelLabel());
    }

    public function test_returns_fallback_for_model_label_when_translation_missing()
    {
        app('translator')->addLines([], 'en');
        app()->setLocale('en');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            protected static bool $hasTitleCaseModelLabel = true;
            public static function getResourceKey(): string { return 'alert-channel'; }
        };

        $this->assertEquals('Alert Channel', $resource::getModelLabel());
    }

    public function test_returns_translation_for_plural_model_label_when_available()
    {
        app('translator')->addLines([
            'resources.test-resource.plural_label' => 'Тестовые ресурсы'
        ], 'ru');

        app()->setLocale('ru');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            public static function getResourceKey(): string { return 'test-resource'; }
        };

        $this->assertEquals('Тестовые ресурсы', $resource::getPluralModelLabel());
    }

    public function test_returns_fallback_for_plural_model_label_when_translation_missing()
    {
        app('translator')->addLines([], 'en');
        app()->setLocale('en');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            protected static bool $hasTitleCaseModelLabel = true;
            public static function getResourceKey(): string { return 'user-management'; }
        };

        $this->assertEquals('User Managements', $resource::getPluralModelLabel());
    }

    public function test_get_resource_key_converts_class_name_correctly()
    {
        $testResource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            public static function getResourceKey(): string
            {
                return \Illuminate\Support\Str::snake('AlertChannel', '-');
            }
        };

        $this->assertEquals('alert-channel', $testResource::getResourceKey());
    }

    public function test_respects_has_title_case_model_label_true()
    {
        app('translator')->addLines([], 'en');
        app()->setLocale('en');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            protected static bool $hasTitleCaseModelLabel = true;
            public static function getResourceKey(): string { return 'user-management'; }
        };

        $this->assertEquals('User Management', $resource::getModelLabel());
    }

    public function test_respects_has_title_case_model_label_false()
    {
        app('translator')->addLines([], 'en');
        app()->setLocale('en');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            protected static bool $hasTitleCaseModelLabel = false;
            public static function getResourceKey(): string { return 'user-management'; }
        };

        $this->assertEquals('User management', $resource::getModelLabel());
    }

    public function test_translation_overrides_title_case_setting()
    {
        app('translator')->addLines([
            'resources.test-resource.label' => 'канал уведомлений'
        ], 'ru');

        app()->setLocale('ru');

        $resource = new class extends BaseResource {
            protected static ?string $model = \stdClass::class;
            protected static bool $hasTitleCaseModelLabel = false;
            public static function getResourceKey(): string { return 'test-resource'; }
        };

        $this->assertEquals('канал уведомлений', $resource::getModelLabel());
    }
}