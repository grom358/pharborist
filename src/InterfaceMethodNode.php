<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends StatementNode implements InterfaceStatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ParameterListNode
   */
  protected $parameters;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->getParameters();
  }

  protected function insertChild(Node $node) {
    static $visibilityTypes = [T_PUBLIC, T_PROTECTED, T_PRIVATE];
    if ($node instanceof TokenNode) {
      if ($node->getType() === '&') {
        $this->reference = $node;
      }
      elseif (in_array($node->getType(), $visibilityTypes)) {
        $this->visibility = $node;
      }
      elseif ($node->getType() === T_STATIC) {
        $this->static = $node;
      }
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

  public function appendChild(Node $node) {
    parent::appendChild($node);
    $this->insertChild($node);
  }
}
