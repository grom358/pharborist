<?php
namespace Pharborist;

abstract class Renderer {
  public function render(NodeInterface $node) {
    $method = 'render' . get_class($node);

    if (method_exists($this, $method)) {
      return call_user_func([$this, $method], $node);
    }
    elseif (method_exists($node, '__toString')) {
      return $node->__toString();
    }
    else {
      throw new \LogicException('Cannot render unknown node type ' . get_class($node));
    }
  }

  protected function renderUnaryOperationNode(UnaryOperationNode $node) {
    return $node->getOperator() . ' ' . $operation->getOperand();
  }

  protected function renderBinaryOperationNode(BinaryOperationNode $node) {
    return $node->getLeftOperand() . ' ' . $node->getOperator() . ' ' . $node->getRightOperand();
  }
}
