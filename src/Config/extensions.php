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
return array(

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
    'enabled' => array(
        'TwigBridge\Extension\Loader\Facades',
        'TwigBridge\Extension\Loader\Filters',
        'TwigBridge\Extension\Loader\Functions',
        'TwigBridge\Extension\Auth',
        'TwigBridge\Extension\Config',
        'TwigBridge\Extension\Form',
        'TwigBridge\Extension\Html',
        'TwigBridge\Extension\Session',
        'TwigBridge\Extension\String',
        'TwigBridge\Extension\Translator',
        'TwigBridge\Extension\Url',
       // 'TwigBridge\Extension\Legacy\Facades',
    ),

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
    |     'Form' => array(
    |         'is_safe' => array(
    |             'open'
    |         )
    |     )
    | </code>
    |
    | The values of the `is_safe` array must match the called method on the facade
    | in order to be marked as safe.
    |
    */
    'facades' => array(
        'Auth',
        'Config',
        'Form' => array(
            'is_safe' => array(
                'open',
            )
        ),
        'HTML',
        'Input',
        'Lang',
        'Route',
        'Session',
        'Str',
        'URL',
    ),

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
    |     'link_to' => array(
    |         'is_safe' => array('html')
    |     )
    | </code>
    |
    | The options array also takes a `callback` that allows you to name the
    | function differently in your Twig templates than what is actually called.
    |
    | <code>
    |     'link' => array(
    |         'callback' => 'link_to'
    |     )
    | </code>
    |
    */
    'functions' => array(

    ),

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
    |     'studly_case' => array(
    |         'is_safe' => array('html')
    |     )
    | </code>
    |
    | The options array also takes a `callback` that allows you to name the
    | filter differently in your Twig templates than what is actually called.
    |
    | <code>
    |     'snake' => array(
    |         'callback' => 'snake_case'
    |     )
    | </code>
    |
    */
    'filters' => array(

    ),

);
