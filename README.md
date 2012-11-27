Installation
------------

Add `rcrowe\twigbridge` as a requirement to composer.json:

```javascript
{
    "require": {
        "rcrowe\twigbridge": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Once Composer has installed or updated your packages you need to register TwigBridge with Laravel itself. Open up app/config/app.php and find the providers key towards the bottom and add:

```php
'TwigBridge\TwigServiceProvider'
```

Configuration
-------------

TwigBridge's configuration file can be extended by creating `app/config/packages/rcrowe/twigbridge.php`. You can find the default configuration file at vendor/rcrowe/twigbridge/src/config/twigbridge.php.

You can quickly publish a configuration file by running the following Artisan command.

```php
$ php artisan config:publish rcrowe/twigbridge
```

Extensions
----------

Sometimes you want to extend / add new functions for use in Twig templates. Add to the `exensions` array a list of extensions for Twig to load.

```php
'extensions' => array(
    'TwigBridge\Extensions\Example'
)
```

Intergration
------------

You are able to call all Laravel functions in your Twig templates, TwigBridge does this by making all classes defined as `aliases` in `app/config/app.php` as accessible.

To use the Laravel intergration (or indeed any aliased class and method), your function in Twig must use the format `class_method(...)`. So the Twig function {{ url_to(...) }} will call the class and method `URL::to(...)`.

You can define shortcuts to these by changing the `alias_shortcuts` config parameter. For example, calling `url(...)` is actually an alias to `url_to(...)`.

Package Intergration
--------------------

Other packages can also alter the way Twig is configured, by listening for the event `twigbridge.twig`. For example, if another object wanted to add its own Twig functions is could do the following:

```php
Event::listen('twigbridge.twig', function($twig) {
    $twig->addExtension( new TwigBridge\Extensions\Example );
});
```

Artisan
-------

TwigBridge offers a number of CLI interactions.

List Twig & Bridge versions:
```php
$ php artisan twig
```

Empty the Twig cache:
```php
$ php artisan twig:clean
```
