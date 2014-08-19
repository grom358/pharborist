<?php
namespace Pharborist;

/**
 * Trait for nodes that have arguments. For example, function calls.
 */
trait ArgumentTrait {
  /**
   * @var ArgumentListNode
   */
  protected $arguments;

  /**
   * @return ArgumentListNode
   */
  public function getArgumentList() {
    return $this->arguments;
  }

  /**
   * @return ExpressionNode[]
   */
  public function getArguments() {
    return $this->arguments->asArray();
  }

  /**
   * @param ExpressionNode $argument
   * @return $this
   */
  public function appendArgument(ExpressionNode $argument) {
    $this->arguments->appendArgument($argument);
    return $this;
  }

  /**
   * @param ExpressionNode $argument
   * @return $this
   */
  public function prependArgument(ExpressionNode $argument) {
    $this->arguments->prependArgument($argument);
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
    $this->arguments->insertArgument($argument, $index);
    return $this;
  }

  /**
   * Remove all arguments.
   *
   * @return $this
   */
  public function clearArguments() {
    $this->arguments->clearArguments();
    return $this;
  }
}
