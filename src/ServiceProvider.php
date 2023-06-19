<?php

namespace JustBetter\StatamicMetadataTemplates;

use JustBetter\StatamicMetadataTemplates\Listeners\SetEntryMetaTemplateListener;
use Statamic\Events\EntrySaved;
use Statamic\Providers\AddonServiceProvider;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\MetaTemplateCollections;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        EntrySaved::class => [
            SetEntryMetaTemplateListener::class
        ]
    ];

    public function register(): void
    {
        $this->registerConfig();
    }

    public function bootAddon()
    {
        $this
            ->bootPublishables()
            ->bootCustomFieldTypes();
    }

    public function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/justbetter-meta-templates.php', 'justbetter-meta-templates');

        return $this;
    }

    public function bootPublishables(): static
    {
        $this->publishes([
            __DIR__.'/../resources/blueprints/collections' => resource_path('blueprints/collections'),
            __DIR__.'/../resources/content/collections' => base_path('content/collections'),
            __DIR__.'/../resources/fieldsets' => resource_path('fieldsets'),
        ], 'collections');

        $this->publishes([
            __DIR__.'/../config/justbetter-meta-templates.php' => config_path('justbetter-meta-templates.php'),
        ], 'config');

        return $this;
    }

    public function bootCustomFieldTypes(): self
    {
        MetaTemplateCollections::register();

        return $this;
    }
}
