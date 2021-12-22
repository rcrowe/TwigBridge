<?php

namespace TwigBridge\Tests\Node;

use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\LoaderInterface;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityPolicy;
use Twig\Template;
use Twig\Test\NodeTestCase;
use TwigBridge\Node\GetAttrNode;

class GetAttrTest extends NodeTestCase
{
    public function testNodeConstructor()
    {
        $expr = new NameExpression('foo', 1);
        $attr = new ConstantExpression('bar', 1);
        $args = new ArrayExpression([], 1);
        $args->addElement(new NameExpression('foo', 1));
        $args->addElement(new ConstantExpression('bar', 1));

        $node = $this->getNode($expr, $attr, $args, Template::ARRAY_CALL);

        $this->assertEquals($expr, $node->getNode('node'));
        $this->assertEquals($attr, $node->getNode('attribute'));
        $this->assertEquals($args, $node->getNode('arguments'));
        $this->assertEquals(Template::ARRAY_CALL, $node->getAttribute('type'));
    }

    public function testGetAttributeOnModel()
    {
        $twig = new Environment($this->createMock(LoaderInterface::class));
        $template = new TemplateForTest($twig);

        $object = new TemplateModel;
        $object->attr = 'test';

        $actual = GetAttrNode::attribute($twig, $template->getSourceContext(), $object, "attr", [], Template::ANY_CALL);
        $this->assertEquals('test', $actual);

        $object->attr = null;
        $actual = GetAttrNode::attribute($twig, $template->getSourceContext(), $object, "attr", [], Template::ANY_CALL);
        $this->assertEquals(null, $actual);

        $actual = GetAttrNode::attribute($twig, $template->getSourceContext(), $object, "exists");
        $this->assertEquals(false, $actual);

        $object->exists = true;
        $actual = GetAttrNode::attribute($twig, $template->getSourceContext(), $object, "exists");
        $this->assertEquals(true, $actual);
    }

    public function testGetAttributeOnModelWithSandbox()
    {
        $twig = new Environment($this->createMock(LoaderInterface::class));
        $policy = new SecurityPolicy([], [], [/*method*/], [/*prop*/], []);
        $twig->addExtension(new SandboxExtension($policy, true));
        $template = new TemplateForTest($twig);

        $object = new TemplateModel;
        $object->attr = 'test';

        try {
            GetAttrNode::attribute(
                $twig,
                $template->getSourceContext(),
                $object,
                "attr",
                [],
                Template::ANY_CALL,
                false,
                false,
                true
            );
            $this->fail();
        } catch (SecurityError $e) {
            $this->assertTrue(strpos($e->getMessage(), 'is not allowed') !== false);
        }

        $object->attr = null;

        try {
            GetAttrNode::attribute(
                $twig,
                $template->getSourceContext(),
                $object,
                "attr",
                [],
                Template::ANY_CALL,
                false,
                false,
                true
            );
            $this->fail();
        } catch (SecurityError $e) {
            $this->assertTrue(strpos($e->getMessage(), 'is not allowed') !== false);
        }
    }

    public function getTests()
    {
        $tests = [];

        $expr = new NameExpression('foo', 1);
        $attr = new ConstantExpression('bar', 1);
        $args = new ArrayExpression([], 1);
        $node = $this->getNode($expr, $attr, $args, Template::ANY_CALL);
        $tests[] = [
            $node,
            sprintf(
                '%s%s, "bar", [], "any", false, false, false, 1)',
                $this->getAttributeGetter(),
                $this->getVariableGetter('foo', 1)
            )
        ];

        $node = $this->getNode($expr, $attr, $args, Template::ARRAY_CALL);
        $tests[] = [
            $node,
            '(($__internal_%s = // line 1' . "\n"
                . '($context["foo"] ?? null))'
                . ' && is_array($__internal_%s)'
                . ' || $__internal_%s instanceof ArrayAccess ? ($__internal_%s["bar"] ?? null) : null)',
            null,
            true,
        ];

        $args = new ArrayExpression([], 1);
        $args->addElement(new NameExpression('foo', 1));
        $args->addElement(new ConstantExpression('bar', 1));
        $node = $this->getNode($expr, $attr, $args, Template::METHOD_CALL);
        $tests[] = [
            $node,
            sprintf(
                '%s%s, "bar", [0 => %s, 1 => "bar"], "method", false, false, false, 1)',
                $this->getAttributeGetter(),
                $this->getVariableGetter('foo', 1),
                $this->getVariableGetter('foo')
            )
        ];

        return $tests;
    }

    protected function getAttributeGetter()
    {
        return 'TwigBridge\Node\GetAttrNode::attribute($this->env, $this->source, ';
    }

    protected function getNode($expr, $attr, $args, $type, $lineno = 1)
    {
        $nodes = ['node' => $expr, 'attribute' => $attr, 'arguments' => $args];
        $attributes = [
            'type'                  => $type,
            'is_defined_test'       => false,
            'ignore_strict_check'   => false,
            'optimizable'           => true
        ];

        return new GetAttrNode($nodes, $attributes, $lineno);
    }
}
