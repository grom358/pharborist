<?php
namespace Pharborist;

use Pharborist\Types\ArrayElementNode;

/**
 * Interface for nodes that represent expressions.
 *
 * An expression is any snippet of code which represents or produces a value.
 * Expressions include, but aren't limited to:
 *
 * - Variables: `$mork`
 * - Assignments: `$foo = $bar`
 * - Arithmetic: `$a = $b + $c`
 * - Function or method calls: `foo(); $foo->baz();`
 * - Logical expressions: `($a && $b)`
 * - Function call arguments: `foo(--$baz)`
 * - Comparisons: `$a > $b`
 *
 * Expressions are "smaller" than statements, in the sense that a statement
 * is usually composed of least one expression. Expressions can contain other
 * expressions -- a nested function call, for instance. Any node that implements
 * this interface is considered by PHP to be an expression.
 */
interface ExpressionNode extends NodeInterface, ArrayElementNode {

}
