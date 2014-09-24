<?php

/**
 * @file
 * Contains \Pharborist\Functions\ArgumentTrait.
 */

namespace Pharborist\Functions;

use Pharborist\ExpressionNode;
use Pharborist\CommaListNode;

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
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->arguments->getItems();
  }

  /**
   * @param ExpressionNode $argument
   * @return $this
   */
  public function appendArgument(ExpressionNode $argument) {
    $this->arguments->appendItem($argument);
    return $this;
  }

  /**
   * @param ExpressionNode $argument
   * @return $this
   */
  public function prependArgument(ExpressionNode $argument) {
    $this->arguments->prependItem($argument);
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
