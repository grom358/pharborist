<?php
namespace Pharborist\Functions;

use Pharborist\ExpressionNode;
use Pharborist\CommaListNode;
use Pharborist\Node;
use Pharborist\NodeCollection;

/**
 * Trait for nodes that have arguments. For example, function calls.
 */
trait ArgumentTrait {
  /**
   * @var CommaListNode
   */
  protected $arguments;

  /**
   * @return CommaListNode
   */
  public function getArgumentList() {
    return $this->arguments;
  }

  /**
   * @return NodeCollection|ExpressionNode[]
   */
  public function getArguments() {
    return $this->arguments->getItems();
  }

  /**
   * @param mixed $argument
   *  The argument to prepend. Can be an ExpressionNode or a scalar value,
   *  which will be converted to an expression.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function appendArgument($argument) {
    if (is_scalar($argument)) {
      $argument = Node::fromValue($argument);
    }

    if ($argument instanceof ExpressionNode) {
      $this->arguments->appendItem($argument);
    }
    else {
      throw new \InvalidArgumentException();
    }

    return $this;
  }

  /**
   * @param mixed $argument
   *  The argument to prepend. Can be an ExpressionNode or a scalar value,
   *  which will be converted to an expression.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function prependArgument($argument) {
    if (is_scalar($argument)) {
      $argument = Node::fromValue($argument);
    }

    if ($argument instanceof ExpressionNode) {
      $this->arguments->prependItem($argument);
    }
    else {
      throw new \InvalidArgumentException();
    }

    return $this;
  }

  /**
   * Insert argument before argument at index.
   *
   * @param ExpressionNode $argument
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @return $this
   */
  public function insertArgument(ExpressionNode $argument, $index) {
    $this->arguments->insertItem($argument, $index);
    return $this;
  }

  /**
   * Remove all arguments.
   *
   * @return $this
   */
  public function clearArguments() {
    $this->arguments->clear();
    return $this;
  }
}
