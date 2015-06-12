<?php
namespace Pharborist\Index;

class FunctionIndex extends BaseIndex {
  /**
   * @var ParameterIndex[]
   */
  protected $parameters;

  /**
   * @var bool
   */
  protected $hasReturnTypes;

  /**
   * @var string[]
   */
  protected $returnTypes;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param ParameterIndex[] $parameters
   * @param bool $hasReturnTypes
   * @param string[] $return_types
   */
  public function __construct(FilePosition $position, $name, $parameters, $hasReturnTypes, $return_types) {
    parent::__construct($position, $name);
    $this->parameters = $parameters;
    $this->hasReturnTypes = $hasReturnTypes;
    $this->returnTypes = $return_types;
  }

  /**
   * Get the parameters of function/method.
   *
   * @return ParameterIndex[]
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * Get whether function has phpDoc return types.
   *
   * @return bool
   */
  public function hasReturnTypes() {
    return $this->hasReturnTypes;
  }

  /**
   * Get the return types of function/method.
   *
   * @return string[]
   */
  public function getReturnTypes() {
    return $this->returnTypes;
  }

  /**
   * Set the return types of function/method.
   *
   * @internal Used by Indexer.
   *
   * @param string[] $returnTypes
   * @return $this
   */
  public function setReturnTypes(array $returnTypes) {
    $this->returnTypes = $returnTypes;
    $this->hasReturnTypes = TRUE;
    return $this;
  }

}
