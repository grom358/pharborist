<?php
namespace Pharborist;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  use FunctionTrait;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * Set the name of the declared function.
   *
   * @param string $name
   *   New function name.
   * @return $this
   */
  public function setName($name) {
    /** @var TokenNode $function_name */
    $function_name = $this->getName()->firstChild();
    $function_name->setText($name);
    return $this;
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
