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
    ),

    /*
    |--------------------------------------------------------------------------
    | Facades
    |--------------------------------------------------------------------------
    |
    | Available facades. Access like `{{ Config.get('foo.bar') }}`.
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
        'Str',
        'URL',

        // 'Auth',
        // 'Config',
        // 'Form' => array(
        //     'is_safe' => array(
        //         'open',
        //     ),
        // ),
        // 'HTML' => array(
        //     'is_safe' => true,
        // ),
        // 'Input',
        // 'Lang',
        // 'Route',
        // 'Str',
        // 'URL',
    ),

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    |
    | Available functions. Access like `{{ secure_url(...) }}`.
    |
    */
    'functions' => array(
        'route',
        'action',
        'asset',
        'url',
        'link_to' => array('is_safe' => array('html')),
        'link_to_asset' => array('is_safe' => array('html')),
        'link_to_route' => array('is_safe' => array('html')),
        'link_to_action' => array('is_safe' => array('html')),
        'secure_asset' => array('is_safe' => array('html')),
        'secure_url',
        'trans',
        'trans_choice',
        'csrf_token',

        // 'route',
        // 'action',
        // 'asset' => 'asset_url',
        // 'url' => array(
        //     'callback' => 'router_url',
        //     'is_safe'  => true,
        // ),
        // 'link_to',
        // 'link_to_asset',
        // 'link_to_route',
        // 'link_to_action',
        // 'secure_asset',
        // 'secure_url',
        // 'trans',
        // 'trans_choice',
        // 'csrf_token',
    ),

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | Available filters. Access like `{{ variable|filter }}`.
    |
    */
    'filters' => array(
        'camel_case',
        'snake_case',
        'studly_case',
        'str_finish',
        'str_plural',
        'str_singular',

        // 'camel_case',
        // 'snake_case',
        // 'studly' => 'studly_case',
        // 'str_finish' => array(
        //     'callback' => 'foo_bar',
        //     'is_safe'  => true,
        //     // 'pre_escape' => 'html',
        //     // 'is_safe'    => array('html'),
        // ),
        // 'str_plural',
        // 'str_singular',
    ),

);
