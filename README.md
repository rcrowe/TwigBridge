Allows you to use [Twig](http://twig.sensiolabs.org/) seamlessly in [Laravel 4](http://laravel.com/).

[![Latest Stable Version](https://poser.pugx.org/rcrowe/twigbridge/v/stable.png)](https://packagist.org/packages/rcrowe/twigbridge)
[![Total Downloads](https://poser.pugx.org/rcrowe/twigbridge/downloads.png)](https://packagist.org/packages/rcrowe/twigbridge)
[![Build Status](https://travis-ci.org/rcrowe/TwigBridge.png?branch=master)](https://travis-ci.org/rcrowe/TwigBridge)
[![Coverage Status](https://coveralls.io/repos/rcrowe/TwigBridge/badge.png?branch=0.6)](https://coveralls.io/r/rcrowe/TwigBridge?branch=0.6)
[![License](https://poser.pugx.org/rcrowe/twigbridge/license.png)](https://packagist.org/packages/rcrowe/twigbridge)

Installation
============

Add `rcrowe\twigbridge` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe/twigbridge": "0.6.*"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register TwigBridge with Laravel itself. Open up app/config/app.php and find the providers key towards the bottom and add:

```php
'TwigBridge\ServiceProvider'
```

**TODO:** Add facade instructions.

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
// Without the file extension
View::make('i_am_twig', array(...))
```

TwigBridge also supports views in other packages:

```php
View::make('pagination::simple')
```

The above rules continue when extending another Twig template:

```html
{% extend "parent" %}
{% extend "pagination::parent" %}
```

Extensions
==========

Sometimes you want to extend / add new functions for use in Twig templates. Add to the `enabled` array in config/extensions.php a list of extensions for Twig to load.

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

- TwigBridge\Extensions\FilterLoader
- TwigBridge\Extensions\FunctionsLoader
- TwigBridge\Extensions\FacadeLoader

These extensions are configured by default:

- [Twig_Extension_Debug](http://twig.sensiolabs.org/doc/extensions/debug.html)
- TwigBridge\Extensions\FilterLoader
- TwigBridge\Extensions\FunctionsLoader
- TwigBridge\Extensions\FacadeLoader


FilterLoader and FunctionLoader
-----------

These loader extensions exposes Laravel helpers as both Twig functions and filters.

Check out the config/extensions.php file to see a list of defined function / filters. You can also add your own.

FacadeLoader
-----------

The FacadeLoader extension allows you to call any facade you have configured in config/extensions.php. This gives your Twig templates integration with any Laravel class as well as any other classes you alias.

To use the Laravel integration (or indeed any aliased class and method), just add your facades to the config and call them like `URL.to(link)` (insted of `URL::to($link)`)

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

TwigBridge offers a command for CLI Interaction.

Empty the Twig cache:
```
$ php artisan twig:clean
```

