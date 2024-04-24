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

use Illuminate\Foundation\Vite as IlluminateVite;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels string class in your Twig templates.
 */
class Vite extends AbstractExtension
{
    /**
     * @var string|object
     */
    protected $callback = 'Illuminate\Foundation\Vite';

    /**
     * Return the string object callback.
     *
     * @return string|object
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set a new string callback.
     *
     * @param string|object
     *
     * @return void
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Vite';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'vite',
                function (...$arguments) {
                    $arguments ??= '()';

                    $html = app(IlluminateVite::class)($arguments);

                    return $html->toHtml();
                }
            ),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [];
    }
}
