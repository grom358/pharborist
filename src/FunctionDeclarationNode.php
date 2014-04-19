<?php
namespace Pharborist;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  use FullyQualifiedNameTrait;
  use FunctionTrait;

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
