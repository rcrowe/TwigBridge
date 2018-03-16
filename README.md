Seamlessly implement [Twig](https://twig.symfony.com/) in [Laravel 5](https://laravel.com/).

[![Latest Stable Version](https://poser.pugx.org/rcrowe/twigbridge/v/stable.png)](https://packagist.org/packages/rcrowe/twigbridge)
[![Total Downloads](https://poser.pugx.org/rcrowe/twigbridge/downloads.png)](https://packagist.org/packages/rcrowe/twigbridge)
[![License](https://poser.pugx.org/rcrowe/twigbridge/license.png)](https://packagist.org/packages/rcrowe/twigbridge)

# Requirements

| Laravel   | TwigBridge |
| --------- | ---------- |
| 5.x       | >=0.7      |
| 4.2 / 4.1 | 0.6.*      |
| 4.0       | 0.5.*      |

# Installation
Require this package with [Composer](https://getcomposer.org/):

```bash
$ composer require rcrowe/twigbridge
```

# Quick Start
Once Composer has installed or updated your packages you need to register TwigBridge with
Laravel itself. Open up `config/app.php` and find the `providers` key, towards the end of
the file, and add `TwigBridge\ServiceProvider::class`, to the end:

```php
'providers' => [
     // ...

     TwigBridge\ServiceProvider::class,
],
```

Now find the aliases key, again towards the end of the file, and add 
`'Twig' => TwigBridge\Facade\Twig::class` to have easier access to the `TwigBridge`
(or `Twig_Environment`) class:

```php
'aliases' => [
    // ...

    'Twig' => TwigBridge\Facade\Twig::class,
],
```

Now that you have both of those lines added to `config/app.php` we will use Artisan to
publish this package's configuration file:

```php
$ php artisan vendor:publish --provider="TwigBridge\ServiceProvider"
```

You can begin using Twig like you would any other view:

```twig
// resources/views/hello.twig
<h1>{{ 'Hello, world' }}<h1>
```

```php
// app/Http/routes.php
Route::get('/', function () {
    return view('hello');
});
```

# Configuration
To tell this package to load your Twig files from multiple locations, update the `paths` array
in `config/view.php`.

Your Twig files can have any of the file extensions configured in `config/twigbridge.php`
under the `twig.file_extensions` key. By default, `.html.twig` and `.twig` are supported.

# Installation on Lumen
For Lumen, you need to load the same Service Provider, but you have to disable the `Auth`,
`Translator` and `Url` extensions in your local configuration. Copy the `config/twigbridge.php`
file to your local `config` folder and register the configuration and Service Provider in
`bootstrap/app.php`:

```php
$app->configure('twigbridge'); 
$app->register('TwigBridge\ServiceProvider');
```

# Usage
You call the Twig template like you would any other view:

```php
view('i_am_twig', [...]);
```

TwigBridge also supports views in other packages:

```php
view('pagination::simple');
```

The above rules continue when extending another Twig template:

```twig
{% extends "parent" %}
{% extends "pagination::parent" %}
```

You can call functions with parameters:

```twig
{{ link_to_route('tasks.edit', 'Edit', task.id, {'class': 'btn btn-primary'}) }}
```

All output variables are escaped by default. Use the `raw` filter to skip escaping.

```twig
{{ some_var }}
{{ html_var | raw }}
{{ long_var | str_limit(50) }}
```

# Extensions
Sometimes you want to extend or add new functions to use in your Twig templates. To do this,
add a list of extensions for Twig to load to the `enabled` array in `config/twigbridge.php`:

```php
'enabled' => [
    'TwigBridge\Extensions\Example',
]
```

TwigBridge supports both a string or a closure as a callback, so for example you might
implement the [Assetic](https://github.com/kriswallsmith/assetic) Twig extension as
follows:

```php
'enabled' => [
    function ($app) {
        $factory = new Assetic\Factory\AssetFactory($app['path'].'/../some/path/');
        $factory->setDebug(false);

        // etc...

        return new Assetic\Extension\Twig\AsseticExtension($factory);
    }
]
```

TwigBridge comes with the following extensions enabled by default:

- [`Twig_Extension_Debug`](http://twig.sensiolabs.org/doc/extensions/debug.html)
- `TwigBridge\Extension\Laravel\Auth`
- `TwigBridge\Extension\Laravel\Config`
- `TwigBridge\Extension\Laravel\Dump`
- `TwigBridge\Extension\Laravel\Form`
- `TwigBridge\Extension\Laravel\Html`
- `TwigBridge\Extension\Laravel\Input`
- `TwigBridge\Extension\Laravel\Session`
- `TwigBridge\Extension\Laravel\String`
- `TwigBridge\Extension\Laravel\Translator`
- `TwigBridge\Extension\Laravel\Url`
- `TwigBridge\Extension\Loader\Facades`
- `TwigBridge\Extension\Loader\Filters`
- `TwigBridge\Extension\Loader\Functions`

To enable `0.5.*` style Facades, enable the Legacy Facades extension:
- `TwigBridge\Extension\Laravel\Legacy\Facades`

## FilterLoader and FunctionLoader

These loader extensions expose Laravel helpers as both Twig functions and filters. Check
out the `config/twigbridge.php` file to see a list of defined functions and filters. You
can also add your own.

## FacadeLoader

The FacadeLoader extension allows you to call any facade you have configured in
`config/twigbridge.php`. This gives your Twig templates integration with any Laravel class
as well as any other classes you alias.

To use the Laravel integration (or indeed any aliased class and method), add your facades
to the config and call them like `URL.to(link)` (instead of `URL::to($link)`).

## Functions/Filters/Variables

The following helpers/filters are added by the default Extensions. They are based on 
Laravel's standard helper functions.

Functions:
 * `asset`, `action`, `url`, `route`, `secure_url`, `secure_asset`
 * `auth_check`, `auth_guest`, `auth_user`
 * `config_get`, `config_has`
 * `dump`
 * `form_*` (All the `Form::*` methods, snake_cased)
 * `html_*` (All the `Html::*` methods, snake_cased)
 * `input_get`, `input_old`, `input_has`
 * `link_to`, `link_to_asset`, `link_to_route`, `link_to_action`
 * `session_has`, `session_get`, `csrf_token`, `csrf_field`, `method_field`
 * `str_*` (All the `Str::*` methods, snake_cased)
 * `trans`, `trans_choice`
 * `url_*` (All the `URL::*` methods, snake_cased)

Filters:
 * `camel_case`, `snake_case`, `studly_case`
 * `str_*` (All the `Str::*` methods, snake_cased)
 * `trans`, `trans_choice`

Global variables:
 * `app`: the `Illuminate\Foundation\Application` object
 * `errors`: The `$errors` `MessageBag` from the Validator

# Artisan Commands

TwigBridge also offers two Artisan commands to aid development:

```bash
# Empty the Twig cache:
$ php artisan twig:clean

# Lint all Twig templates:
$ php artisan twig:lint
```
