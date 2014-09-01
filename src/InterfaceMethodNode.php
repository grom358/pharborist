<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends StatementNode implements InterfaceStatementNode {
  use MethodTrait;

  protected function childInserted(Node $node) {
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
}
