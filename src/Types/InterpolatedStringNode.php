<?php
namespace Pharborist\Types;

use Pharborist\ParentNode;
use Pharborist\ExpressionNode;

/**
 * An interpolated string, containing variables or expressions.
 * Example: `"Hello there $name, welcome to Pharborist."`
 */
class InterpolatedStringNode extends ParentNode implements ExpressionNode {

}
