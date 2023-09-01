<?php

namespace JustBetter\StatamicMetadataTemplates\Computed;

use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Entries\Entry;
use Statamic\Facades\Site;
use Statamic\Statamic;

trait MetaTemplate
{
    public static function register()
    {
        if (!Statamic::isCpRoute()) {
            $metadataFields = config('justbetter-meta-templates.fields') ?? [];
            foreach (config('justbetter-meta-templates.collections') ?? [] as $collection) {
                foreach ($metadataFields as $field => $addonField) {
                    Collection::computed($collection, $field, function ($entry, $value) use ($metadataFields, $field, $addonField) {
                        if ($value) {
                            return $value;
                        }

                        $taxonomies = $entry?->collection?->taxonomies()?->pluck('handle') ?? collect();

                        $metaTemplateQuery = Entry::query()
                            ->where('site', Site::current()?->handle())
                            ->where('collection', 'meta_templates')
                            ->where('for_collection', $entry->collection->handle());

                        $taxonomies->each(function ($taxonomyHandle) use ($metaTemplateQuery, $entry) {
                            if ($entry->get($taxonomyHandle)){
                                $metaTemplateQuery->orWhere('terms', 'like', '%' . $entry->$taxonomyHandle->id . '%');
                            }
                        });

                        $metaTemplate = $metaTemplateQuery->orderBy('terms')->first();

                        if (!$metaTemplate || !isset($metadataFields[$field])) {
                            return $value;
                        }

                        $templateData = $metaTemplate->get($metadataFields[$field]);

                        $replaceArray = [];

                        foreach ($entry->data() as $field => $value) {
                            if (!$field || !$value || is_array($value) || is_object($value)) {
                                continue;
                            }

                            $replaceArray['{' . $field . '}'] = $value;
                        }

                        return strtr($templateData, $replaceArray);
                    });
                }
            }
        }
    }
}
