<?php

namespace JustBetter\StatamicMetadataTemplates\Computed;

use Statamic\Facades\Collection;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Entries\Entry;
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

                        $metaTemplate = EntryFacade::query()->where('collection', 'meta_templates')->where('for_collection', $entry->collection->handle())->first();

                        if (!$metaTemplate) {
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

                        return str_replace(array_keys($replaceArray), array_values($replaceArray), $templateData);
                    });
                }
            }
        }
    }
}
