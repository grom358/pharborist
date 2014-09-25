<?php
namespace Pharborist;

/**
 * An anonymous function (closure).
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  use ParameterTrait;

  /**
   * @var TokenNode
   */
  protected $reference;

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

  protected function childInserted(Node $node) {
    if ($node instanceof TokenNode && $node->getType() === '&') {
      $this->reference = $node;
    }
  }
}
