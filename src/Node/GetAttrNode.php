<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace TwigBridge\Node;

use Twig\Compiler;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\Source;
use Twig\Template;

/**
 * Compile a custom twig_get_attribute node.
 *
 * See:
 *  - https://github.com/rcrowe/TwigBridge/issues/362
 *  - https://github.com/rcrowe/TwigBridge/issues/265
 */
class GetAttrNode extends GetAttrExpression
{
    /**
     * @inheritdoc
     */
    public function __construct(array $nodes = [], array $attributes = [], int $lineno = 0, string $tag = null)
    {
        // Skip parent::__construct()
        Node::__construct($nodes, $attributes, $lineno, $tag);
    }

    /**
     * @inheritdoc
     */
    public function compile(Compiler $compiler): void
    {
        $env = $compiler->getEnvironment();

        // optimize array calls
        if ($this->getAttribute('optimizable')
            && (!$env->isStrictVariables() || $this->getAttribute('ignore_strict_check'))
            && !$this->getAttribute('is_defined_test')
            && Template::ARRAY_CALL === $this->getAttribute('type')
        ) {
            $var = '$'.$compiler->getVarName();
            $compiler
                ->raw('(('.$var.' = ')
                ->subcompile($this->getNode('node'))
                ->raw(') && is_array(')
                ->raw($var)
                ->raw(') || ')
                ->raw($var)
                ->raw(' instanceof ArrayAccess ? (')
                ->raw($var)
                ->raw('[')
                ->subcompile($this->getNode('attribute'))
                ->raw('] ?? null) : null)')
            ;

            return;
        }

        // START EDIT
        // This is the only line that should be different to the parent function.
        $compiler->raw(static::class . '::attribute($this->env, $this->source, ');
        // END EDIT

        if ($this->getAttribute('ignore_strict_check')) {
            $this->getNode('node')->setAttribute('ignore_strict_check', true);
        }

        $compiler
            ->subcompile($this->getNode('node'))
            ->raw(', ')
            ->subcompile($this->getNode('attribute'))
        ;

        if ($this->hasNode('arguments')) {
            $compiler->raw(', ')->subcompile($this->getNode('arguments'));
        } else {
            $compiler->raw(', []');
        }

        $compiler->raw(', ')
            ->repr($this->getAttribute('type'))
            ->raw(', ')->repr($this->getAttribute('is_defined_test'))
            ->raw(', ')->repr($this->getAttribute('ignore_strict_check'))
            ->raw(', ')->repr($env->hasExtension(SandboxExtension::class))
            ->raw(', ')->repr($this->getNode('node')->getTemplateLine())
            ->raw(')')
        ;
    }

    /**
     * Returns the attribute value for a given array/object.
     *
     * @param Environment $env
     * @param Source $source
     * @param mixed  $object            The object or array from where to get the item
     * @param mixed  $item              The item to get from the array or object
     * @param array  $arguments         An array of arguments to pass if the item is an object method
     * @param string $type              The type of attribute (@see \Twig\Template constants)
     * @param bool   $isDefinedTest     Whether this is only a defined check
     * @param bool   $ignoreStrictCheck Whether to ignore the strict attribute check or not
     * @param bool   $sandboxed
     * @param int    $lineno            The template line where the attribute was called
     *
     * @return mixed The attribute value, or a Boolean when $isDefinedTest is true, or null when the attribute is not
     *               set and $ignoreStrictCheck is true
     *
     * @throws RuntimeError if the attribute does not exist and Twig is running in strict mode
     *         and $isDefinedTest is false
     */
    public static function attribute(
        Environment $env,
        Source $source,
        $object,
        $item,
        array $arguments = [],
        $type = /* Template::ANY_CALL */ 'any',
        $isDefinedTest = false,
        $ignoreStrictCheck = false,
        $sandboxed = false,
        int $lineno = -1
    ) {
        if (Template::METHOD_CALL !== $type
            && (is_a($object, 'Illuminate\Database\Eloquent\Model') || is_a($object, 'Livewire\Component'))
        ) {
            // We can't easily find out if an attribute actually exists, so return true
            if ($isDefinedTest) {
                return true;
            }

            if ($env->hasExtension(SandboxExtension::class)) {
                $env->getExtension(SandboxExtension::class)->checkPropertyAllowed($object, $item);
            }

            // Call the attribute, the Model object does the rest of the magic
            return $object->$item;
        }

        // Note: Since twig:3.9 the 'twig_get_attribute' function was renamed to CoreExtension::getAttribute.
        //       Because this is an internal function of twig, the authors could break it in a minor version.
        if (!function_exists('twig_get_attribute')) {
            return CoreExtension::getAttribute($env, $source, $object, $item, $arguments, $type, $isDefinedTest, $ignoreStrictCheck);
        }

        return \twig_get_attribute(
            $env,
            $source,
            $object,
            $item,
            $arguments,
            $type,
            $isDefinedTest,
            $ignoreStrictCheck,
            $sandboxed,
            $lineno
        );
    }
}
