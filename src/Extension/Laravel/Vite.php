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
 * Access Laravels vite class in your Twig templates.
 */
class Vite extends AbstractExtension
{
    /**
     * @var \Illuminate\Foundation\Vite
     */
    protected $vite;

    /**
     * Create a new Vite extension
     *
     * @param \Illuminate\Foundation\Vite
     */
    public function __construct(IlluminateVite $vite)
    {
        $this->vite = $vite;
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
                [$this->vite, '__invoke'],
                [
                    'is_safe' => [
                        'html',
                    ],
                ],
            ),
        ];
    }
}
