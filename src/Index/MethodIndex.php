<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class MethodIndex extends FunctionIndex {

  /**
   * @var string
   */
  private $visibility;

  /**
   * Get the visibility of the method.
   *
   * @return string
   *   Either public, protected or private.
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param string $visibility
   * @param ParameterIndex[]  $parameters
   * @param string[] $returnTypes
   */
  public function __construct($position, $name, $visibility, $parameters, $returnTypes) {
    $this->sourcePosition = $position;
    $this->name = $name;
    $this->visibility = $visibility;
    $this->parameters = $parameters;
    $this->returnTypes = $returnTypes;
  }

}
