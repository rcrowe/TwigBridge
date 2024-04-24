<?php


namespace TwigBridge\Node;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\View\ViewName;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Node;

#[YieldReady]
class EventNode extends Node
{

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->write(
                '$context = ' . EventNode::class . '::triggerLaravelEvents($this->getTemplateName(), $context);'
            )
            ->raw("\n");
    }

    public static function triggerLaravelEvents(string $templateName, array &$context): array
    {
        if (Str::endsWith($templateName, '.twig')) {
            $templateName = Str::substr($templateName, 0, mb_strlen($templateName) - 5);
        }
        /** @var \Illuminate\View\Factory $factory */
        $env = resolve('view');
        $viewName = ViewName::normalize($templateName);

        $view = new View(
            $env,
            $env->getEngineResolver()->resolve('twig'),
            $viewName,
            null,
            $context
        );
        $env->callCreator($view);
        $env->callComposer($view);
        return $view->getData();
    }
}
