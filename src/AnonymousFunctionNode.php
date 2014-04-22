<?php
namespace Pharborist;

/**
 * An anonymous function.
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var ParameterListNode
   */
  protected $parameters;

  /**
   * @var CommaListNode
   */
  protected $lexicalVariables;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @return ParameterListNode
   */
  public function getParameterList() {
    return $this->parameters;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->getParameters();
  }

  /**
   * @return LexicalVariableNode[]
   */
  public function getLexicalVariables() {
    return $this->lexicalVariables->getItems();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }

  protected function insertChild(Node $node) {
    if ($node instanceof TokenNode && $node->getType() === '&') {
      $this->reference = $node;
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
