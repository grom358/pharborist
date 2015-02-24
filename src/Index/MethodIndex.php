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
  public function __construct(SourcePosition $position, $name, $visibility, $parameters, $returnTypes) {
    parent::__construct($position, $name, $parameters, $returnTypes);
    $this->visibility = $visibility;
  }

}
