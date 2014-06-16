<?php

/**
 * Brings Twig to Laravel 4.
 *
 * @author Rob Crowe <hello@vivalacrowe.com>
 * @license MIT
 */

namespace TwigBridge\View;

use Illuminate\View\View as BaseView;
use TwigBridge\Engines\TwigEngine;

/**
 * Pass to TwigEngine the request view, not just the path.
 *
 * We need the view name, normally you only have access to the path, in order
 * to set the correct template name when Twig compiles. This name is then
 * used to check for view composers.
 */
class View extends BaseView
{
    /**
     * {@inheritdoc}
     */
    protected function getContents()
    {
        // We need to pass in the original view requested not just the path
        // which Illuminate\View\View does in order for Twig loader to get access
        // to view composers.
        //TODO: There must be a better way to do this?

        // Pass globals to Twig's global scope
        if ($this->engine instanceof TwigEngine)
        {
            $twig = $this->engine->getTwig();
            foreach ($this->factory->getShared() as $key => $value)
            {
                $twig->addGlobal($key, $value);
            }
        }

        return $this->engine->get($this->path, $this->gatherData(), $this->view);
    }
}
