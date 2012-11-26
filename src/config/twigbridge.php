<?php

return array(
    'delimiters' => array(
        'tag_comment'  => array('{#', '#}'),
        'tag_block'    => array('{%', '%}'),
        'tag_variable' => array('{{', '}}'),
    ),
    'environment' => array(
        'debug'               => false,
        'charset'             => 'utf-8',
        'base_template_class' => 'Twig_Template',
        'cache'               => null,
        'auto_reload'         => true,
        'strict_variables'    => false,
        'autoescape'          => false,
        'optimizations'       => -1,
    ),
);