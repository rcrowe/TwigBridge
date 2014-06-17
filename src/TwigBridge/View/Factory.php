<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\View;

use Illuminate\View\Factory as BaseFactory;

/**
 * Overrides default environment object so that we can override view object.
 */
class Factory extends BaseFactory
{
    /**
     * {@inheritdoc}
     */
    public function make($view, $data = array(), $mergeData = array())
    {
        $path = $this->finder->find($view);
        $data = array_merge($mergeData, $this->parseData($data));

        return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
    }
}
