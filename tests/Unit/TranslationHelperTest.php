<?php

namespace Qsque\FilamentTranslationHelper\Tests\Unit;

use Qsque\FilamentTranslationHelper\Support\TranslationHelper;
use Qsque\FilamentTranslationHelper\Tests\TestCase;

class TranslationHelperTest extends TestCase
{
    public function test_returns_translation_when_available()
    {
        app('translator')->addLines([
            'resources.user.fields.name' => 'User Name'
        ], 'en');

        $result = TranslationHelper::getWithFallback('resources.user.fields.name');

        $this->assertEquals('User Name', $result);
    }

    public function test_returns_fallback_when_translation_missing()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.user.fields.first_name');

        $this->assertEquals('First Name', $result);
    }

    public function test_handles_resource_label_keys()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.alert-channel.label');

        $this->assertEquals('Alert Channel', $result);
    }

    public function test_handles_resource_plural_label_keys()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.user-profile.plural_label');

        $this->assertEquals('User Profile', $result);
    }

    public function test_handles_nested_keys()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('common.fields.custom_field');

        $this->assertEquals('Custom Field', $result);
    }

    public function test_respects_title_case_parameter()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.user.fields.first_name', true);
        $this->assertEquals('First Name', $result);

        $result = TranslationHelper::getWithFallback('resources.user.fields.first_name', false);
        $this->assertEquals('First name', $result);
    }

    public function test_handles_hyphenated_keys()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.alert-channel.fields.bot-token');

        $this->assertEquals('Bot Token', $result);
    }

    public function test_handles_underscored_keys()
    {
        app('translator')->addLines([], 'en');

        $result = TranslationHelper::getWithFallback('resources.user.fields.created_at');

        $this->assertEquals('Created At', $result);
    }
}