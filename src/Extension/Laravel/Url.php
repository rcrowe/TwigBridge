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

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str as IlluminateStr;
use Illuminate\Routing\Router;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels url class in your Twig templates.
 */
class Url extends AbstractExtension
{
    /**
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Create a new url extension
     *
     * @param \Illuminate\Routing\UrlGenerator
     * @param \Illuminate\Routing\Router
     */
    public function __construct(UrlGenerator $url, Router $router)
    {
        $this->url = $url;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Url';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('asset', [$this->url, 'asset'], ['is_safe' => ['html']]),
            new TwigFunction('action', [$this->url, 'action'], ['is_safe' => ['html']]),
            new TwigFunction('url', [$this, 'url'], ['is_safe' => ['html']]),
            new TwigFunction('route', [$this->url, 'route'], ['is_safe' => ['html']]),
            new TwigFunction('route_has', [$this->router, 'has'], ['is_safe' => ['html']]),
            new TwigFunction('secure_url', [$this->url, 'secure'], ['is_safe' => ['html']]),
            new TwigFunction('secure_asset', [$this->url, 'secureAsset'], ['is_safe' => ['html']]),
            new TwigFunction(
                'url_*',
                function ($name) {
                    $arguments = array_slice(func_get_args(), 1);
                    $name      = IlluminateStr::camel($name);

                    return call_user_func_array([$this->url, $name], $arguments);
                }
            ),
        ];
    }

    public function url($path = null, $parameters = [], $secure = null)
    {
        if (! $path) {
            return $this->url;
        }

        return $this->url->to($path, $parameters, $secure);
    }
}
