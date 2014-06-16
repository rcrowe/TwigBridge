<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Configuration options for the built-in extensions.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | Enabled extensions.
    |
    | `Twig_Extension_Debug` is enabled automatically if twig.debug is TRUE.
    |
    */
    'enabled' => [
        'TwigBridge\Extension\Loader\Facades',
        'TwigBridge\Extension\Loader\Filters',
        'TwigBridge\Extension\Loader\Functions',
    ],

    /*
    |--------------------------------------------------------------------------
    | Facades
    |--------------------------------------------------------------------------
    |
    | Available facades. Access like `{{ Config.get('foo.bar') }}`.
    |
    | Each facade can take an optional array of options. To mark the whole facade
    | as safe you can set the option `'is_safe' => true`. Setting the facade as
    | safe means that any HTML returned will not be escaped.
    |
    | It is advisable to not set the whole facade as safe and instead mark the
    | each appropriate method as safe for security reasons. You can do that with
    | the following syntax:
    |
    | <code>
    |     'Form' => [
    |         'is_safe' => [
    |             'open'
    |         ]
    |     ]
    | </code>
    |
    | The values of the `is_safe` array must match the called method on the facade
    | in order to be marked as safe.
    |
    */
    'facades' => [
        'Auth',
        'Config',
        'Form' => [
            'is_safe' => [
                'open',
            ],
        ],
        'HTML',
        'Input',
        'Lang',
        'Route',
        'Session',
        'Str',
        'URL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    |
    | Available functions. Access like `{{ secure_url(...) }}`.
    |
    | Each function can take an optional array of options. These options are
    | passed directly to `Twig_SimpleFunction`.
    |
    | So for example, to mark a function as safe you can do the following:
    |
    | <code>
    |     'link_to' => [
    |         'is_safe' => ['html']
    |     ]
    | </code>
    |
    | The options array also takes a `callback` that allows you to name the
    | function differently in your Twig templates than what it's actually called.
    |
    | <code>
    |     'link' => [
    |         'callback' => 'link_to'
    |     ]
    | </code>
    |
    */
    'functions' => [
        'route',
        'action',
        'asset',
        'url',
        'link_to'        => ['is_safe' => ['html']],
        'link_to_asset'  => ['is_safe' => ['html']],
        'link_to_route'  => ['is_safe' => ['html']],
        'link_to_action' => ['is_safe' => ['html']],
        'secure_asset'   => ['is_safe' => ['html']],
        'secure_url',
        'trans',
        'trans_choice',
        'csrf_token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | Available filters. Access like `{{ variable|filter }}`.
    |
    | Each filter can take an optional array of options. These options are
    | passed directly to `Twig_SimpleFilter`.
    |
    | So for example, to mark a filter as safe you can do the following:
    |
    | <code>
    |     'studly_case' => [
    |         'is_safe' => ['html']
    |     ]
    | </code>
    |
    | The options array also takes a `callback` that allows you to name the
    | filter differently in your Twig templates than what is actually called.
    |
    | <code>
    |     'snake' => [
    |         'callback' => 'snake_case'
    |     ]
    | </code>
    |
    */
    'filters' => [
        'camel_case',
        'snake_case',
        'studly_case',
        'str_finish',
        'str_plural',
        'str_singular',
    ],

];
