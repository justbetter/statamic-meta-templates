<?php

namespace JustBetter\StatamicMetadataTemplates\Actions;

use Statamic\Entries\Entry;

class SetEntryMetadata
{
    public function updateData(Entry $entry, string $metaTitle, string $metaDescription): void
    {
        $contentVariables = [
            '{title}' => $entry?->get('title'),
        ];

        foreach ($contentVariables as $search => $replace) {
            $metaTitle = str_replace($search, $replace, $metaTitle);
            $metaDescription = str_replace($search, $replace, $metaDescription);
        }

        $metadata = [];

        if ($metaTitle) {
            $metadata['meta_title'] = $metaTitle;
        }

        if ($metaDescription) {
            $metadata['meta_description'] = $metaDescription;
        }

        if ($metadata) {
            $entry->merge($metadata)->saveQuietly();
        }
    }
}
