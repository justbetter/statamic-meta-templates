<?php

namespace JustBetter\StatamicMetadataTemplates;

use Closure;
use Statamic\Events\EntrySaved;
use Statamic\Providers\AddonServiceProvider;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\Text;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\Textarea;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\MetaTemplateCollections;
use JustBetter\StatamicMetadataTemplates\Listeners\SetEntryMetaTemplateListener;
use JustBetter\StatamicMetadataTemplates\Routing\UrlBuilder;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\AardvarkSeoMetaTitleFieldtype;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\AardvarkSeoMetaDescriptionFieldtype;
use JustBetter\StatamicMetadataTemplates\Fieldtypes\Entry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Statamic;
use Statamic\Support\Str;
use Illuminate\Support\Facades\Route;

class ServiceProvider extends AddonServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
    }

    public function bootComputed(): self
    {
        if (!Statamic::isCpRoute()) {
            $metadataFields = config('justbetter-meta-templates.fields') ?? [];
            foreach (config('justbetter-meta-templates.collections') ?? [] as $collection) {
                foreach ($metadataFields as $field => $addonField) {
                    Collection::computed($collection, $field, function ($entry, $value) use ($metadataFields, $field, $addonField) {
                        if ($value) {
                            return $value;
                        }

                        $metaTemplate = EntryFacade::query()->where('collection', 'meta_templates')->where('for_collection', $entry->collection->handle())->first();

                        if (!$metaTemplate) {
                            return $value;
                        }

                        $templateData = $metaTemplate->get($metadataFields[$field]);

                        return str_replace(['{title}'], [$entry->title], $templateData);
                    });
                }
            }
        }

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
