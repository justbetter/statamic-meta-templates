<?php

namespace JustBetter\StatamicMetadataTemplates;

use Statamic\Providers\AddonServiceProvider;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\MetaTemplateCollections;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Statamic;
use JustBetter\StatamicMetadataTemplates\Computed\MetaTemplate;

class ServiceProvider extends AddonServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function bootComputed(): self
    {
        MetaTemplate::register();
        
        return $this;
    }

    public function bootAddon(): void
    {
        $this
            ->bootComputed()
            ->bootPublishables()
            ->bootCustomFieldTypes();
    }

    public function registerConfig(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/justbetter-meta-templates.php', 'justbetter-meta-templates');

        return $this;
    }

    public function bootPublishables(): self
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
