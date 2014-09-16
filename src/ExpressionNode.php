<?php
namespace Pharborist;

/**
 * Interface for nodes that represent expressions.
 *
 * <p>An expression is any snippet of code which represents or produces a value.
 * Expressions include (but aren't limited to):</p>
 * <ul>
 *  <li>Variables: <code>$mork</code></li>
 *  <li>Assignments: <code>$foo = $bar</code></li>
 *  <li>Arithmetic: <code>$a = $b + $c</code></li>
 *  <li>Function calls: <code>foo()</code></li>
 *  <li>Logical expressions: <code>($a && $b)</code></li>
 *  <li>Function call arguments: <code>foo(--$baz)</code>
 * </ul>
 * <p>Expressions are &quot;smaller&quot; than statements, in the sense that a
 * statement is usually composed of at least one expression; expressions can
 * also contain other expressions (for instance, a nested function call). Any
 * node that implements ExpressionNode is considered an expression by PHP.</p>
 */
interface ExpressionNode extends NodeInterface, ArrayElementNode {

}
