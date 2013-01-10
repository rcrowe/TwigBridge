Allows you to use [Twig](http://twig.sensiolabs.org/) seamlessly in [Laravel 4](http://laravel.com/).

Installation
------------


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
