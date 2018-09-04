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

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Gate extends AbstractExtension
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
            new TwigFunction('can', [$this->gate, 'check']),
        ];
    }
}
