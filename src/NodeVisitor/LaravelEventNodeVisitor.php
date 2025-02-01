<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TwigBridge\NodeVisitor;

use LogicException;
use Twig\Environment;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;
use TwigBridge\Node\EventNode;

class LaravelEventNodeVisitor implements NodeVisitorInterface
{

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            try {
                $parentNode = $node->getNode('parent');
                //https://regex101.com/r/4PQe3r/1
                $isEmbedded = !!preg_match('/$\s*{%\s*embed/m', $parentNode->getSourceContext()->getCode());
            } catch (LogicException $e) {
                $isEmbedded = false;
            }
            if (!$isEmbedded) {
                $displayStartNodes = $node->getNode('display_start');
                $displayStartNodes->setNode(count($displayStartNodes), new EventNode());
            }
        }
        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}
