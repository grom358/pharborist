<?php
namespace Pharborist;

/**
 * A function parameter.
 */
class ParameterNode extends ParentNode {
  /**
   * @var Node
   */
  protected $typeHint;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $value;

  protected function insertChild(Node $node) {
    if ($node instanceof TokenNode) {
      if ($node->getType() === T_ARRAY || $node->getType() === T_CALLABLE) {
        $this->typeHint = $node;
      }
      elseif ($node->getType() === '&') {
        $this->reference = $node;
      }
    }
    elseif ($node instanceof NameNode) {
      $this->typeHint = $node;
    }
  }

  protected function insertBeforeChild(Node $child, Node $node) {
    parent::insertBeforeChild($child, $node);
    $this->insertChild($node);
  }

  protected function insertAfterChild(Node $child, Node $node) {
    parent::insertAfterChild($child, $node);
    $this->insertChild($node);
  }

  protected function prependChild(Node $node) {
    parent::prependChild($node);
    $this->insertChild($node);
  }

  protected function appendChild(Node $node) {
    parent::appendChild($node);
    $this->insertChild($node);
  }

  /**
   * @return Node
   */
  public function getTypeHint() {
    return $this->typeHint;
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }
}
