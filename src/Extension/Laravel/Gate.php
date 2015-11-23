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
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Gate extends Twig_Extension
{
    /**
     * @var GateContract
     */
    protected $gate;

    /**
     * Create a new gate extension.
     *
     * @param GateContract $gate
     */
    public function __construct(GateContract $gate)
    {
        $this->gate = $gate;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Gate';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('can', [$this->gate, 'check']),
        ];
    }
}
