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

use Collective\Html\HtmlBuilder;
use Illuminate\Support\Str as IlluminateStr;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels html builder in your Twig templates.
 */
class Html extends AbstractExtension
{
    /**
     * @var \Collective\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Create a new html extension
     *
     * @param \Collective\Html\HtmlBuilder
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
            new TwigFunction('link_to', [$this->html, 'link'], ['is_safe' => ['html']]),
            new TwigFunction('link_to_asset', [$this->html, 'linkAsset'], ['is_safe' => ['html']]),
            new TwigFunction('link_to_route', [$this->html, 'linkRoute'], ['is_safe' => ['html']]),
            new TwigFunction('link_to_action', [$this->html, 'linkAction'], ['is_safe' => ['html']]),
            new TwigFunction(
                'html_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = IlluminateStr::camel($name);

                    return call_user_func_array([$this->html, $name], $arguments);
                },
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }
}
