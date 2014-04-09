<?php
namespace Pharborist;

/**
 * A new expression.
 *
 * For example, new MyClass()
 */
class NewNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'className' => NULL,
    'arguments' => array(),
  );

  /**
   * @return NamespacePathNode
   */
  public function getClassName() {
    return $this->properties['className'];
  }

  /**
   * @return Node[]
   */
  public function getArguments() {
    return $this->properties['arguments'];
  }
}
