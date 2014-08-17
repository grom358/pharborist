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

  protected function childInserted(Node $node) {
    if ($node instanceof TokenNode && $node->getType() === '&') {
      $this->reference = $node;
    }
  }
}
