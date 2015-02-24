<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class FunctionIndex extends BaseIndex {
  /**
   * @var ParameterIndex[]
   */
  protected $parameters;

  /**
   * @var string[]
   */
  protected $returnTypes;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param ParameterIndex[] $parameters
   * @param string[] $return_types
   */
  public function __construct($position, $name, $parameters, $return_types) {
    parent::__construct($position, $name);
    $this->parameters = $parameters;
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
   * Get the return types of function/method.
   *
   * @return string[]
   */
  public function getReturnTypes() {
    return $this->returnTypes;
  }
}
