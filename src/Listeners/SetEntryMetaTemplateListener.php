<?php

namespace JustBetter\StatamicMetadataTemplates\Listeners;

use Illuminate\Support\Collection;
use JustBetter\StatamicMetadataTemplates\Actions\SetEntryMetadata;
use Statamic\Entries\Entry;
use Statamic\Events\EntrySaved;
use Statamic\Taxonomies\LocalizedTerm;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Entries\EntryCollection;

class SetEntryMetaTemplateListener
{
    public function __construct(public SetEntryMetadata $setEntryMetadata)
    {
    }

    public function handle(EntrySaved $event): void
    {
        $entry = $event->entry;

        if (!$entry || $entry?->collection()?->handle() !== 'meta_templates') {
            return;
        }

        $this->updateMetaTemplate($entry);
    }

    public function updateMetaTemplate(Entry $entry): Collection
    {
        if (!$entry?->for_collection) {
            return collect();
        }

        $metaTitle = $entry?->meta_title;
        $metaDescription = $entry?->meta_description;

        /** @var EntryCollection $entries */
        $entries = EntryFacade::query()
            ->where('collection', $entry?->for_collection)
            ->where('site', $entry?->site()?->handle() ?? 'default')
            ->get();

        foreach ($entries as $_entry) {
            $this->setEntryMetadata->updateData($_entry, $metaTitle, $metaDescription);
        }

        return $entries->collect()->pluck('id');
    }
}
