Allows you to use [Twig](http://twig.sensiolabs.org/) seamlessly in [Laravel 4](http://laravel.com/).

[![Build Status](https://travis-ci.org/rcrowe/TwigBridge.png?branch=master)](https://travis-ci.org/rcrowe/TwigBridge)

Installation
============

Add `rcrowe\twigbridge` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe/twigbridge": "0.1.*"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register TwigBridge with Laravel itself. Open up app/config/app.php and find the providers key towards the bottom and add:

```php
'TwigBridge\TwigServiceProvider'
```

Configuration
=============

TwigBridge's configuration file can be extended by creating `app/config/packages/rcrowe/twigbridge/config.php`. You can find the default configuration file at vendor/rcrowe/twigbridge/src/config/config.php.

You can quickly publish a configuration file by running the following Artisan command.

```
$ php artisan config:publish rcrowe/twigbridge
```

Usage
=====

You call the Twig template like you would any other view:

```php
View::make('i_am_twig.twig', array(...))

// You don't even need to pass the extension
View::make('i_am_twig', array(...))
```

TwigBridge also supports views in other packages:

```php
View::make('pagination::simple')
```

The above rules continue when extending another Twig template:

```html
{% extend "parent.twig" %}
{% extend "parent" %}
{% extend "pagination::parent" %}
```

Extensions
==========

Sometimes you want to extend / add new functions for use in Twig templates. Add to the `exensions` array a list of extensions for Twig to load.

```php
'extensions' => array(
    'TwigBridge\Extensions\Example'
)
```

TwigBridge supports both a string or a closure as a callback, so for example you might implement the [Assetic](https://github.com/kriswallsmith/assetic) Twig extension as follows:

```php
'extensions' => array(
    function($app) {
        $factory = new Assetic\Factory\AssetFactory($app['path'].'/../some/path/');
        $factory->setDebug(false);
        // etc.....
        return new Assetic\Extension\Twig\AsseticExtension($factory);
    }
)
```

TwigBridge comes with the following extensions:

- TwigBridge\Extensions\AliasLoader
- TwigBridge\Extensions\Html

AliasLoader
-----------

The AliasLoader extension allows you to call any class that has been aliased in your `app/config/app.php` file. This gives your Twig templates intergration with any Laravel call as well as any other classes you alias.

To use the Laravel intergration (or indeed any aliased class and method), your function in Twig must use the format `class_method(...)`. So the Twig function {{ url_to(...) }} will call the class and method `URL::to(...)`.

You can define shortcuts to these by changing the `alias_shortcuts` config parameter. For example, calling `url(...)` is actually an alias to `url_to(...)`.

Html
----

Intergrates Meido [HTML](https://github.com/meido/html) and [Form](https://github.com/meido/form), which means you can for example do the following:

```html
{{ form_open() }}
```

which will then output the following HTML:

```html
<form method="POST" action="http://example.com/current/uri" accept-charset="utf-8">
```

Events
======

TwigBridge fires the `twigbridge.twig` event just before the TwigEngine is registered, this gives other packages or your application time to alter Twigs behaviour; maybe another package wants to add an extension or change the lexer used. To do this just register and event handler:

```php
Event::listen('twigbridge.twig', function($twig) {
    $twig->addExtension( new TwigBridge\Extensions\Example );
});
```

Artisan Commands
================

TwigBridge offers a number of CLI interactions.

List Twig & Bridge versions:
```
$ php artisan twig
```

Empty the Twig cache:
```
$ php artisan twig:clean
```

Pre-compile Twig templates:
```
$ php artisan twig:compile
```

Check syntax of Twig templates:
```
$ php artisan twig:lint
```
