<?php

namespace TwigBridge\View;

use Illuminate\View\View as LaravelView;
use Throwable;

/**
 * @property \TwigBridge\Engine\Twig $engine
 */
class View extends LaravelView
{
    public function fragment($fragment)
    {
        try {
            $this->factory->incrementRender();
            $this->factory->callComposer($this);

            /** @var \TwigBridge\Engine\Compiler */
            $compiler = $this->engine->getCompiler();
            $contents = $compiler->load($this->path)->renderBlock($fragment, $this->gatherData());

            $this->factory->decrementRender();
            $this->factory->flushStateIfDoneRendering();

            return $contents;
        } catch (Throwable $e) {
            $this->factory->flushState();

            throw $e;
        }
    }
}
