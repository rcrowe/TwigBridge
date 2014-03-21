<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\Extension\Loader;

/**
 * Extension to expose defined facades to the Twig templates.
 *
 * See the `extensions.php` config file, specifically the `facades` key
 * to configure those that are loaded.
 *
 * Use the following syntax for using a facade in your application.
 *
 * <code>
 *     {{ Facade.method(param, ...) }}
 *     {{ Config.get('app.timezone') }}
 * </code>
 */
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
