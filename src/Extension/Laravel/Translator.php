<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Access Laravels translator class in your Twig templates.
 */
class Translator extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Translator';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $translator = app(LaravelTranslator::class);
        return [
            new Twig_SimpleFunction('trans', [$translator, 'trans']),
            new Twig_SimpleFunction('trans_choice', [$translator, 'transChoice']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $translator = app(LaravelTranslator::class);
        return [
            new Twig_SimpleFilter(
                'trans',
                [$translator, 'trans'],
                [
                    'pre_escape' => 'html',
                    'is_safe'    => ['html'],
                ]
            ),
            new Twig_SimpleFilter(
                'trans_choice',
                [$translator, 'transChoice'],
                [
                    'pre_escape' => 'html',
                    'is_safe'    => ['html'],
                ]
            ),
        ];
    }
}
