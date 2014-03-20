<?php

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
        'TwigBridge\Extension\FunctionLoader',
        'TwigBridge\Extension\FilterLoader',
        'TwigBridge\Extension\FacadeLoader',
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
            ),
        ),
        'HTML' => array(
            'is_safe' => true,
        ),
        'Input',
        'Lang',
        'Route',
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
    */
    'functions' => array(
        'route',
        'action',
        'asset',
        'url',
        'link_to',
        'link_to_asset',
        'link_to_route',
        'link_to_action',
        'secure_asset',
        'secure_url',
        'trans',
        'trans_choice',
        'csrf_token',
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
    ),

);
