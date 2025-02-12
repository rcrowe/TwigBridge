<?php

namespace TwigBridge\Tests\Node;

use Twig\Environment;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

class TemplateForTest extends Template
{
    private $name;

    public function __construct(Environment $env, $name = 'index.twig')
    {
        parent::__construct($env);
        $this->name = $name;
    }

    public function getZero()
    {
        return 0;
    }

    public function getEmpty()
    {
        return '';
    }

    public function getString()
    {
        return 'some_string';
    }

    public function getTrue()
    {
        return true;
    }

    public function getTemplateName(): string
    {
        return $this->name;
    }

    public function getDebugInfo(): array
    {
        return [];
    }

    protected function doGetParent(array $context): bool|string|self|TemplateWrapper
    {
        return false;
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        return [];
    }

    /**
     * Returns information about the original template source code.
     *
     * @return Source
     */
    public function getSourceContext(): Source
    {
        return new Source('', $this->getTemplateName());
    }
}
