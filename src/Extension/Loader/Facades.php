<?php

namespace TwigBridge\Extension\Loader;

class Facades extends Loader
{
    /**
     * Returns the name of the extension.
     *
     * @return string Extension name.
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Loader_Facades';
    }

    /**
     * Get globals this extension provides.
     *
     * Currently only returns facades.
     *
     * @return array
     */
    public function getGlobals()
    {
        $load    = $this->app['config']->get('twigbridge::extensions.facades', array());
        $globals = array();

        foreach ($load as $facade => $options) {
            list($facade, $callable, $options) = $this->parseCallable($facade, $options);

            $globals[$facade] = new Facade\Caller($facade, $options);
        }

        return $globals;
    }
}
