<?php
namespace Pharborist;

/**
 * Trait for nodes that have parameters. For example, function declarations.
 */
trait ParameterTrait {
  /**
   * @var ParameterListNode
   */
  protected $parameters;

  /**
   * @return ParameterListNode
   */
  public function getParameterList() {
    return $this->parameters;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->asArray();
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function prependParameter(ParameterNode $parameter) {
    $this->parameters->prependParameter($parameter);
    return $this;
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function appendParameter(ParameterNode $parameter) {
    $this->parameters->appendParameter($parameter);
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
    $this->parameters->insertParameter($parameter, $index);
    return $this;
  }

  /**
   * Remove all parameters.
   *
   * @return $this
   */
  public function clearParameters() {
    $this->parameters->clearParameters();
    return $this;
  }
}
