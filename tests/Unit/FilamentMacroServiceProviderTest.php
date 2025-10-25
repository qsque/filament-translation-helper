<?php

namespace Qsque\FilamentTranslationHelper\Tests\Unit;

use Qsque\FilamentTranslationHelper\Providers\FilamentMacroServiceProvider;
use Qsque\FilamentTranslationHelper\Tests\TestCase;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;

class FilamentMacroServiceProviderTest extends TestCase
{
    protected FilamentMacroServiceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new FilamentMacroServiceProvider(App::getInstance());

        app()->setLocale('en');

        $translator = app('translator');
        $translator->addLines([
            'resources.alert-channel.fields.name' => 'Alert Channel Name',
            'resources.alert-channel.sections.general' => 'General Settings',
            'resources.alert-channel.table.columns.type' => 'Alert Type',
            'common.fields.email' => 'Email Address',
            'common.fields.config' => 'Configuration',
        ], 'en');
    }

    #[Test]
    public function it_translates_field_labels_using_resource_specific_translations()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $field = TextInput::make('name');

        $this->assertEquals('Alert Channel Name', $field->getLabel());
    }

    #[Test]
    public function it_falls_back_to_common_field_translations()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $field = TextInput::make('email');

        $this->assertEquals('Email Address', $field->getLabel());
    }

    #[Test]
    public function it_translates_section_headings_using_resource_specific_translations()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $section = Section::make('general');

        $this->assertEquals('General Settings', $section->getHeading());
    }

    #[Test]
    public function it_falls_back_to_common_translations_for_sections()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $section = Section::make('config');

        $this->assertEquals('Configuration', $section->getHeading());
    }

    #[Test]
    public function it_formats_section_heading_when_no_translation_found()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $section = Section::make('unknown_section');

        $this->assertEquals('Unknown Section', $section->getHeading());
    }

    #[Test]
    public function it_translates_table_column_labels_using_multiple_paths()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $column = TextColumn::make('type');

        $this->assertEquals('Alert Type', $column->getLabel());
    }

    #[Test]
    public function it_handles_dotted_field_names()
    {
        $this->mockResourceContext('alert-channel');

        $this->provider->boot();

        $field = TextInput::make('config.bot_token');

        $this->assertEquals('Configuration', $field->getLabel());
    }

    #[Test]
    public function it_does_not_translate_when_no_resource_context()
    {
        $this->provider->boot();

        $field = TextInput::make('name');

        $this->assertEquals('Name', $field->getLabel());
    }

    #[Test]
    public function get_common_field_translation_returns_correct_translation()
    {
        $provider = $this->provider;
        $method = new \ReflectionMethod($provider, 'getCommonFieldTranslation');
        $method->setAccessible(true);

        $result = $method->invoke($provider, 'email');
        $this->assertEquals('Email Address', $result);

        $result = $method->invoke($provider, 'nonexistent_field');
        $this->assertNull($result);
    }

    #[Test]
    public function get_common_field_translation_handles_dotted_names()
    {
        $provider = $this->provider;
        $method = new \ReflectionMethod($provider, 'getCommonFieldTranslation');
        $method->setAccessible(true);

        $result = $method->invoke($provider, 'config.some_setting');
        $this->assertEquals('Configuration', $result);
    }

    #[Test]
    public function provider_is_properly_registered()
    {
        $this->assertInstanceOf(FilamentMacroServiceProvider::class, $this->provider);
        $this->assertTrue(method_exists($this->provider, 'boot'));
    }

    private function mockResourceContext(string $resourceKey): void
    {
        $mockProvider = new class($resourceKey) extends FilamentMacroServiceProvider {
            private string $mockResourceKey;

            public function __construct(string $resourceKey)
            {
                $this->mockResourceKey = $resourceKey;
            }

            protected function getResourceKeyFromContext(): ?string
            {
                return $this->mockResourceKey;
            }
        };

        $this->provider = $mockProvider;
    }
}