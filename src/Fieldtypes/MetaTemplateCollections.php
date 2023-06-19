<?php

namespace JustBetter\StatamicMetadataTemplates\Fieldtypes;

use Statamic\Facades\Collection;
use Statamic\Fieldtypes\Collections;

class MetaTemplateCollections extends Collections
{
    public function getIndexItems($request)
    {
        return Collection::all()->whereIn('handle', config('justbetter-meta-templates.collections'))->sortBy('title')->map(function ($collection) {
            return [
                'id' => $collection->handle(),
                'title' => $collection->title(),
                'entries' => $collection->queryEntries()->count(),
            ];
        })->values();
    }
}
