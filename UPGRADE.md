# TwigBridge Upgrade Guide

## Upgrade v0.12.x -> v0.13.x
Version greater than v0.12.x now require Twig3. There are some feature that were removed in Twig3, so we were forced to remove some features as well in TwigBridge 

 - add `'TwigBridge\Extension\Loader\Globals'` in the configuration twigbridge.extensions.enabled. Or else, you will loose global variables `errors`, `app` and all other shared with `View::share`
 - add `'TwigBridge\Extension\Laravel\Event'` in the configuration twigbridge.extensions.enabled. Or else, `composing:{view name}` and `creating:{view name}` events will no longer be triggered.
 - Remove config 'base_template_class' in config/twigbridge.php. It is no longer possible to use a custom template class.
 - Make sure you no longer support Twig2 deprecated features: https://twig.symfony.com/doc/2.x/deprecated.html

## Upgrade 0.5.x -> 0.6.x

There have been some big changes since the last 0.5.x release. 0.6.x now only supports PHP 5.4+ and is targeted for Laravel 4.2. It probably still works for L4.0/4.1, but this is not tested/supported.

Some of noticable changes are: 
 - The ServiceProvider has been renamed from `TwigBridge\TwigServiceProvider` to `TwigBridge\ServiceProvider`, so update this in your config/app.php
 - A Twig facade is added, see the install instructions.
 - The AliasLoader has been removed, Facades are not automatically called with `class_method(...)`. This has been replaced with:
   * Extensions for the most common Facades, which can be used with the same syntax (see readme for available extensions)
   * The Facade Loader Extension imports Facades which are configured in the package config, and can be called like `Class.method()`
   * The LegacyFacades Extension which can be enabled, to provide backward compatability with the 0.5 style Facades
 - The TwigBridge events are removed. Similar results can be achieved by simply using the Twig facade or IoC bindings
 - The Compile command has been removed. Templates are now automaticcaly compiled on the `php artisan optimize` command.
 - The package configuration has been split in 2 files. Republish the config and re-apply your settings.
 - The getAttributes() functions has changed, make sure you call attributes/methods correctly on your models. `model.attribute` will get the function, `model.attribute()` the Relation object. Other methods on models will also have to use parentheses.
 
For more changes see the changelog in CONTRIBUTING.md
