<?php

namespace TwigBridge\Tests\Node;

use Twig\Environment;
use Twig\Source;
use Twig\Template;

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

    public function getTemplateName()
    {
        return $this->name;
    }

    public function getDebugInfo()
    {
        return [];
    }

    protected function doGetParent(array $context)
    {
        return false;
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
    }

    /**
     * Returns information about the original template source code.
     *
     * @return Source
     */
    public function getSourceContext()
    {
        return new Source('', $this->getTemplateName());
    }
}
