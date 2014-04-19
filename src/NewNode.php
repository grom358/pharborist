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
    'arguments' => NULL,
  );

  /**
   * @return NamespacePathNode
   */
  public function getClassName() {
    return $this->properties['className'];
  }

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    /** @var ArgumentListNode $arguments */
    $arguments = $this->properties['arguments'];
    return $arguments->getArguments();
  }
}
