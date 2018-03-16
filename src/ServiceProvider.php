<?php

/**
 * This file is part of the TwigBridge package.
 *
 * @copyright Robert Crowe <hello@vivalacrowe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge;

use Illuminate\View\ViewServiceProvider;
use InvalidArgumentException;
use Twig_Loader_Array;
use Twig_Loader_Chain;
use TwigBridge\Twig\Normalizers\DefaultNormalizer;
use TwigBridge\Twig\Normalizers\Normalizer;

/**
 * Bootstrap Laravel TwigBridge.
 *
 * You need to include this `ServiceProvider` in your app.php file:
 *
 * <code>
 *     'providers' => [
 *         'TwigBridge\ServiceProvider'
 *     ];
 * </code>
 */
class ServiceProvider extends ViewServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerCommands();
        $this->registerOptions();
        $this->registerLoaders();
        $this->registerEngine();
        $this->registerAliases();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadConfiguration();
        $this->registerExtensions();
    }

    /**
     * Check if we are running Lumen or not.
     *
     * @return bool
     */
    protected function isLumen()
    {
        return strpos($this->app->version(), 'Lumen') !== false;
    }

    /**
     * Check if we are running on PHP 7.
     *
     * @return bool
     */
    protected function isRunningOnPhp7()
    {
        return version_compare(PHP_VERSION, '7.0-dev', '>=');
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../config/twigbridge.php';

        if (!$this->isLumen()) {
            $this->publishes([$configPath => config_path('twigbridge.php')], 'config');
        }

        $this->mergeConfigFrom($configPath, 'twigbridge');
    }

    /**
     * Register the Twig extension in the Laravel View component.
     *
     * @return void
     */
    protected function registerExtensions()
    {
        /** @var \Illuminate\View\Factory $view */
        $view = $this->app['view'];

        /** @var array $extensions */
        $extensions = $this->app['twig.file_extensions'];

        foreach ($extensions as $extension) {
            $view->addExtension($extension, 'twig', function () {
                return $this->app['twig.engine'];
            });
        }
    }

    /**
     * Register console command bindings.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app->bindIf('command.twig', function () {
            return new Command\TwigBridge;
        });

        $this->app->bindIf('command.twig.clean', function () {
            return new Command\Clean;
        });

        $this->app->bindIf('command.twig.lint', function () {
            return new Command\Lint;
        });

        $this->commands('command.twig', 'command.twig.clean', 'command.twig.lint');
    }

    /**
     * Register Twig config option bindings.
     *
     * @return void
     */
    protected function registerOptions()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app['config'];

        $this->app->bindIf('twig.file_extensions', function () use ($config) {
            return $config->get('twigbridge.twig.file_extensions');
        });

        $this->app->bindIf(Normalizer::class, function () {
            return new DefaultNormalizer($this->app['twig.file_extensions']);
        });

        $this->app->alias(Normalizer::class, 'twig.normalizer');

        $this->app->bindIf('twig.options', function () use ($config) {
            $options = $config->get('twigbridge.twig.environment', []);

            if (!isset($options['cache']) || $options['cache'] === null) {
                $options['cache'] = storage_path('framework/views/twig');
            }

            return $options;
        });

        $this->app->bindIf('twig.extensions', function () use ($config) {
            $load = $config->get('twigbridge.extensions.enabled', []);

            $debug = array_get($this->app['twig.options'], 'debug', false);

            if ($debug) {
                array_unshift($load, 'Twig_Extension_Debug');
            }

            return $load;
        });

        $this->app->bindIf('twig.lexer', function () {
            return null;
        });
    }

    /**
     * Register Twig loader bindings.
     *
     * @return void
     */
    protected function registerLoaders()
    {
        $this->app->bindIf('twig.templates', function () {
            return [];
        });

        $this->app->bindIf('twig.loader.array', function ($app) {
            return new Twig_Loader_Array($app['twig.templates']);
        });

        $this->app->bindIf('twig.loader.viewfinder', function () {
            return new Twig\Loader($this->app['files'], $this->app['view']->getFinder(), $this->app['twig.normalizer']);
        });

        $this->app->bindIf('twig.loader', function () {
            return new Twig_Loader_Chain([
                $this->app['twig.loader.array'],
                $this->app['twig.loader.viewfinder'],
            ]);
        }, true);
    }

    /**
     * Register Twig engine bindings.
     *
     * @return void
     */
    protected function registerEngine()
    {
        $this->app->bindIf('twig', function () {
            $extensions = $this->app['twig.extensions'];
            $lexer = $this->app['twig.lexer'];
            $twig = new Bridge(
                $this->app['twig.loader'], $this->app['twig.options'], $this->app['twig.normalizer'], $this->app
            );

            // Instantiate and add extensions
            foreach ($extensions as $extension) {
                // Get an instance of the extension
                // Support for string, closure and an object
                if (is_string($extension)) {
                    try {
                        $extension = $this->app->make($extension);
                    } catch (\Exception $e) {
                        throw new InvalidArgumentException("Cannot instantiate Twig extension '$extension': " . $e->getMessage());
                    }
                } elseif (is_callable($extension)) {
                    $extension = $extension($this->app, $twig);
                } elseif (!is_a($extension, 'Twig_Extension')) {
                    throw new InvalidArgumentException('Incorrect extension type');
                }

                $twig->addExtension($extension);
            }

            // Set lexer
            if (is_a($lexer, 'Twig_LexerInterface')) {
                $twig->setLexer($lexer);
            }

            return $twig;
        }, true);

        $this->app->alias('twig', 'Twig_Environment');
        $this->app->alias('twig', 'TwigBridge\Bridge');

        $this->app->bindIf('twig.compiler', function () {
            return new Engine\Compiler($this->app['twig']);
        });

        $this->app->bindIf('twig.engine', function () {
            return new Engine\Twig($this->app['twig.compiler'], $this->app['twig.loader.viewfinder'], $this->app['config']->get('twigbridge.twig.globals', []));
        });
    }

    /**
     * Register aliases for classes that had to be renamed because of reserved names in PHP7.
     *
     * @return void
     */
    protected function registerAliases()
    {
        if (!$this->isRunningOnPhp7() && !class_exists('TwigBridge\Extension\Laravel\String')) {
            class_alias('TwigBridge\Extension\Laravel\Str', 'TwigBridge\Extension\Laravel\String');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.twig',
            'command.twig.clean',
            'command.twig.lint',
            'twig.extension',
            'twig.options',
            'twig.extensions',
            'twig.lexer',
            'twig.templates',
            'twig.loader.array',
            'twig.loader.viewfinder',
            'twig.loader',
            'twig',
            'twig.compiler',
            'twig.engine',
        ];
    }
}
