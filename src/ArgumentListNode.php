<?php
namespace Pharborist;

/**
 * List of function/method call arguments.
 */
class ArgumentListNode extends ParentNode {
  public function asArray() {
    return $this->childrenByInstance('\Pharborist\ExpressionNode');
  }

  /**
   * Prepend argument.
   *
   * @param ExpressionNode $argument
   * @return $this
   */
  public function prependArgument(ExpressionNode $argument) {
    $arguments = $this->asArray();
    if (empty($arguments)) {
      $this->firstChild()->after($argument);
    }
    else {
      $this->firstChild()->after([
        $argument,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Append argument.
   *
   * @param ExpressionNode $argument
   * @return $this
   */
  public function appendArgument(ExpressionNode $argument) {
    $arguments = $this->asArray();
    if (empty($arguments)) {
      $this->firstChild()->after($argument);
    }
    else {
      $last_argument = $arguments[count($arguments) - 1];
      $last_argument->after([
        Token::comma(),
        Token::space(),
        $argument,
      ]);
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
    $arguments = $this->asArray();
    if (empty($arguments)) {
      if ($index > 0) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $this->firstChild()->after($argument);
    }
    else {
      $max_index = count($arguments) - 1;
      if ($index < 0 || $index > $max_index) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $arguments[$index]->before([
        $argument,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Remove all arguments.
   *
   * @return $this
   */
  public function clearArguments() {
    $this->children()->slice(1, -1)->remove();
  }
}
