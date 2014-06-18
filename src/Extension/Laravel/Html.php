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
use Twig_SimpleFunction;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Support\Str;

/**
 * Access Laravels html builder in your Twig templates.
 */
class Html extends Twig_Extension
{
    /**
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Create a new html extension
     *
     * @param \Illuminate\Html\HtmlBuilder
     */
    public function __construct(HtmlBuilder $html)
    {
        $this->html = $html;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Html';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('link_to', [$html, 'link'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_asset', [$html, 'linkAsset'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_route', [$html, 'linkRoute'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_action', [$html, 'linkAction'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction(
                'html_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = Str::camel($name);

                    return call_user_func_array([$this->html, $name], $arguments);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
