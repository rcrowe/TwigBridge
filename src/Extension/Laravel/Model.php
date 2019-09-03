<?php
/**
 * This file is part of the TwigBridge package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace TwigBridge\Extension\Laravel;

use Twig\Extension\AbstractExtension;
use TwigBridge\NodeVisitor\GetAttrAdjuster;

/**
 * Access to Laravel model properties using ArrayAccess.
 */
class Model extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getNodeVisitors()
    {
        return [
            new GetAttrAdjuster,
        ];
    }
}
