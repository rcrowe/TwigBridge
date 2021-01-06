<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace TwigBridge\NodeVisitor;

use Twig\Environment;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;
use TwigBridge\Node\GetAttrNode;

/**
 * Custom twig_get_attribute node.
 */
class GetAttrAdjuster implements NodeVisitorInterface
{
    /**
     * @inheritdoc
     */
    public function enterNode(Node $node, Environment $env): Node
    {
        // Make sure this is a GetAttrExpression (and not a subclass)
        if (get_class($node) !== GetAttrExpression::class) {
            return $node;
        }

        // Swap it with our custom GetAttrNode
        $nodes = [
            'node' => $node->getNode('node'),
            'attribute' => $node->getNode('attribute')
        ];

        if ($node->hasNode('arguments')) {
            $nodes['arguments'] = $node->getNode('arguments');
        }

        $attributes = [
            'type' => $node->getAttribute('type'),
            'is_defined_test' => $node->getAttribute('is_defined_test'),
            'ignore_strict_check' => $node->getAttribute('ignore_strict_check'),
            'optimizable' => $node->getAttribute('optimizable'),
        ];

        return new GetAttrNode($nodes, $attributes, $node->getTemplateLine(), $node->getNodeTag());
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 0;
    }
}
