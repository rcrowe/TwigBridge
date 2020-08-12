<?php

namespace TwigBridge\Tests\TwigIntegrationTests;

use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Illuminate\View\Factory;
use Illuminate\View\View;
use stdClass;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig\Test\IntegrationTestCase;
use TwigBridge\Bridge;
use TwigBridge\Tests\TwigBridgeTestTrait;

/**
 * Adaptation of Twig IntegrationTestCase to use Laravel's TwigBridge as the compiler.
 *
 * The .test file now support an additional section below the --EXPECT-- section: --EXPECT_EVENT_COUNTS--.
 * It should contains a json containing the expected events creating and composing counts for each template.
 *
 */
class TwigtIntegrationTest extends IntegrationTestCase
{
    use TwigBridgeTestTrait;

    public function getTests($name, $legacyTests = false)
    {
        $tests = parent::getTests($name, $legacyTests);
        return collect($tests)->mapWithKeys(function (array $test) {
            //use the file name as the test name, for convenience only
            return [$test[0] => $test];
        })->all();
    }


    /**
     * @dataProvider getTests
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = '')
    {
        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * Override parent because we need to inject additional initialization and
     * assertion from the parent doIntegrationTest.
     *
     *
     * @param $file
     * @param $message
     * @param $condition
     * @param $templates
     * @param $exception
     * @param $outputs
     * @param string $deprecation
     * @throws Error
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    protected function doIntegrationTest(
        $file,
        $message,
        $condition,
        $templates,
        $exception,
        $outputs,
        $deprecation = ''
    ) {
        if (!$outputs) {
            $this->markTestSkipped('no tests to run');
        }

        if ($condition) {
            eval('$ret = ' . $condition . ';');
            if (!$ret) {
                $this->markTestSkipped($condition);
            }
        }

        $loader = new ArrayLoader($templates);

        foreach ($outputs as $i => $match) {
            [$match[3], $expectedEventsCalled] = $this->extractOutputAndEventCounts($match[3]);


            $laravelApp = $this->buildLaravelApp($loader);
            $twig = $this->getTwig($laravelApp);
            $eventValuesHolder = $this->attachEventListeners($laravelApp, $templates);

            $twig->addGlobal('global', 'global');
            foreach ($this->getRuntimeLoaders() as $runtimeLoader) {
                $twig->addRuntimeLoader($runtimeLoader);
            }

            foreach ($this->getExtensions() as $extension) {
                $twig->addExtension($extension);
            }

            foreach ($this->getTwigFilters() as $filter) {
                $twig->addFilter($filter);
            }

            foreach ($this->getTwigTests() as $test) {
                $twig->addTest($test);
            }

            foreach ($this->getTwigFunctions() as $function) {
                $twig->addFunction($function);
            }

            // avoid using the same PHP class name for different cases
            $p = new \ReflectionProperty(Environment::class, 'templateClassPrefix');
            $p->setAccessible(true);
            $p->setValue($twig, '__TwigTemplate_' . hash('sha256', uniqid(mt_rand(), true), false) . '_');

            $deprecations = [];
            try {
                $prevHandler = set_error_handler(
                    function ($type, $msg, $file, $line, $context = []) use (&$deprecations, &$prevHandler) {
                        if (E_USER_DEPRECATED === $type) {
                            $deprecations[] = $msg;

                            return true;
                        }

                        return $prevHandler ? $prevHandler($type, $msg, $file, $line, $context) : false;
                    }
                );

                $template = $twig->load('index.twig');
            } catch (\Exception $e) {
                if (false !== $exception) {
                    $message = $e->getMessage();
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', \get_class($e), $message)));
                    $last = substr($message, \strlen($message) - 1);
                    $this->assertTrue(
                        '.' === $last || '?' === $last,
                        'Exception message must end with a dot or a question mark.'
                    );

                    return;
                }

                throw new Error(sprintf('%s: %s', \get_class($e), $e->getMessage()), -1, null, $e);
            } finally {
                restore_error_handler();
            }

            $this->assertSame($deprecation, implode("\n", $deprecations));

            try {
                $output = trim($template->render(eval($match[1] . ';')), "\n ");
            } catch (\Exception $e) {
                if (false !== $exception) {
                    $this->assertSame(trim($exception), trim(sprintf('%s: %s', \get_class($e), $e->getMessage())));

                    return;
                }

                $e = new Error(sprintf('%s: %s', \get_class($e), $e->getMessage()), -1, null, $e);

                $output = trim(sprintf('%s: %s', \get_class($e), $e->getMessage()));
            }

            if (false !== $exception) {
                [$class] = explode(':', $exception);
                if (class_exists('PHPUnit\Framework\Constraint\Exception')) {
                    $constraintClass = 'PHPUnit\Framework\Constraint\Exception';
                } else {
                    $constraintClass = 'PHPUnit_Framework_Constraint_Exception';
                }
                $this->assertThat(null, new $constraintClass($class));
            }

            $expected = trim($match[3], "\n ");

            if ($expected !== $output) {
                printf("Compiled templates that failed on case %d:\n", $i + 1);

                foreach (array_keys($templates) as $name) {
                    echo "Template: $name\n";
                    echo $twig->compile($twig->parse($twig->tokenize($twig->getLoader()->getSourceContext($name))));
                }
            }
            $this->assertEquals($expected, $output, $message . ' (in ' . $file . ')');
            $this->assertEventsCalled($eventValuesHolder, $expectedEventsCalled);
        }
    }

    public function getFixturesDir()
    {
        return __DIR__ . '/Fixtures/';
    }

    private function buildLaravelApp(LoaderInterface $loader): Application
    {
        $extensionsConfig = [
            'enabled' => [
                'TwigBridge\Extension\Loader\Facades',
                'TwigBridge\Extension\Loader\Filters',
                'TwigBridge\Extension\Loader\Functions',
            ],
        ];
        $laravelApp = $this->getApplication(['extensions' => $extensionsConfig]);
        $laravelApp['twig.loader.viewfinder'] = $loader;
        $this->addBridgeServiceToApplication($laravelApp);
        return $laravelApp;
    }

    private function getTwig(Application $laravelApp): Bridge
    {
        /** @var Bridge $twig */
        $twig = $laravelApp->get('twig');
        return $twig;
    }

    private function extractOutputAndEventCounts($output)
    {
        $expectedEventsCalled = [];
        if (preg_match('/(.*?)--EXPECT_EVENT_COUNTS--\s*(.*)\s*/s', $output, $splittedMatch)) {
            //The EXPECT section contains the additional EXPECT_EVENT_COUNTS section.
            $output = $splittedMatch[1];
            $expectedEventsCalled = json_decode($splittedMatch[2], true);
            $this->assertSame(
                JSON_ERROR_NONE,
                json_last_error(),
                'EXPECT_EVENT_COUNTS section has this error: ' . json_last_error_msg()
            );
        }
        return [$output, $expectedEventsCalled];
    }

    private function attachEventListeners(Application $laravelApp, array $templates)
    {
        /** @var Factory $viewFactory */
        $viewFactory = $laravelApp['view'];
        $viewFactory->setDispatcher(new Dispatcher());

        $eventValuesHolder = new stdClass();
        $eventValuesHolder->composingCounts = [];
        $eventValuesHolder->creatingCounts = [];
        foreach ($templates as $templateName => $template) {
            //Remove the .twig part
            $viewName = Str::substr($templateName, 0, strlen($templateName) - 5);
            $viewFactory->getDispatcher()->listen(
                "composing: $viewName",
                function (View $view) use ($eventValuesHolder) {
                    $count = $eventValuesHolder->composingCounts[$view->getName()] ?? 0;
                    $eventValuesHolder->composingCounts[$view->getName()] = ++$count;
                    $view['variableFromComposingEvent_' . Str::slug($view->getName())] =
                        "from composing {$view->getName()} event";
                }
            );
            $viewFactory->getDispatcher()->listen(
                "creating: $viewName",
                function (View $view) use ($eventValuesHolder) {
                    $count = $eventValuesHolder->creatingCounts[$view->getName()] ?? 0;
                    $eventValuesHolder->creatingCounts[$view->getName()] = ++$count;
                    $view['variableFromCreatingEvent_' . Str::slug($view->getName())] =
                        "from creating {$view->getName()} event";
                }
            );
        }
        return $eventValuesHolder;
    }

    private function assertEventsCalled($eventValuesHolder, $expectedEventsCalled)
    {
        $this->assertSame($eventValuesHolder->composingCounts, $expectedEventsCalled);
        $this->assertSame($eventValuesHolder->creatingCounts, $expectedEventsCalled);
    }
}
