# Statamic Metadata Templates

> This addon makes it possible to manage metadata per collection

## Description

This addon adds a new collection `Meta templates`, in this collection it's possible to set a default `Meta title` and `Meta description`.

You can choose for which collection you want to add this data, the collections you can choose from are based on what you have entered in the config.

When saving a meta template it will go through all the entries of your chosen collection and update the `Meta title` and `Meta description` fields.

## Note

Most SEO addons will have some basic fields on collections like `meta_title` or `meta_description`.

If you are not using any SEO addons or you don't have these fields on your collection you will have to add these manually.

This addon also only adds the metadata to your entries, it won't display it on your frontend. This should be done by your SEO addon or your theme.

## Installation

After installing this addon you should publish the vendor of this addon as explained below.

## Config

The default collection is set to `pages`, it's possible to change this or add new ones.

To change this you should alter the `collections` array in the config.

## Publishables

You can publish all of the publishables with:

```sh
php artisan vendor:publish --provider="JustBetter\StatamicMetadataTemplates\ServiceProvider"
```

Or publish them individually by using tags:

```sh
php artisan vendor:publish --provider="JustBetter\StatamicMetadataTemplates\ServiceProvider" --tag="collections"
php artisan vendor:publish --provider="JustBetter\StatamicMetadataTemplates\ServiceProvider" --tag="config"
```
