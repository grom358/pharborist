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
      /** @var Node $argument */
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
      /** @var Node $argument */
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
   * @param mixed $argument
   *   The argument to insert. Can be an ExpressionNode or a scalar value,
   *   which will be converted to an expression.
   * @param int $index
   *   Position to insert argument at.
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @throws \InvalidArgumentException
   *
   * @return $this
   */
  public function insertArgument($argument, $index) {
    if (is_scalar($argument)) {
      $argument = Node::fromValue($argument);
    }

    if ($argument instanceof ExpressionNode) {
      /** @var Node $argument */
      $this->arguments->insertItem($argument, $index);
    }
    else {
      throw new \InvalidArgumentException();
    }

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
