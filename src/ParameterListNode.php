<?php
namespace Pharborist;

/**
 * Function/method parameters
 */
class ParameterListNode extends ParentNode {
  public function getParameters() {
    return $this->childrenByInstance('\Pharborist\ParameterNode');
  }

  /**
   * Prepend parameter.
   *
   * @param ParameterNode $parameter
   * @return $this
   */
  public function prependParameter(ParameterNode $parameter) {
    $parameters = $this->getParameters();
    if (empty($parameters)) {
      $this->firstChild()->after($parameter);
    }
    else {
      $this->firstChild()->after([
        $parameter,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Append parameter.
   *
   * @param ParameterNode $parameter
   * @return $this
   */
  public function appendParameter(ParameterNode $parameter) {
    $parameters = $this->getParameters();
    if (empty($parameters)) {
      $this->firstChild()->after($parameter);
    }
    else {
      $last_parameter = $parameters[count($parameters) - 1];
      $last_parameter->after([
        Token::comma(),
        Token::space(),
        $parameter,
      ]);
    }
    return $this;
  }

  /**
   * Insert parameter before parameter at index.
   *
   * @param ParameterNode $parameter
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @return $this
   */
  public function insertParameter(ParameterNode $parameter, $index) {
    $parameters = $this->getParameters();
    if (empty($parameters)) {
      if ($index > 0) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $this->firstChild()->after($parameter);
    }
    else {
      $max_index = count($parameters) - 1;
      if ($index < 0 || $index > $max_index) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $parameters[$index]->before([
        $parameter,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Clear all parameters.
   *
   * @return $this
   */
  public function clearParameters() {
    $this->children()->slice(1, -1)->remove();
    return $this;
  }
}
