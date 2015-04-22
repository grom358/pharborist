<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class MethodIndex extends FunctionIndex {

  /**
   * @var string
   */
  private $visibility;

  /**
   * @var boolean
   */
  private $final;

  /**
   * @var boolean
   */
  private $static;

  /**
   * @var boolean
   */
  private $abstract;

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
   * @param boolean $final
   * @param boolean $static
   * @param boolean $abstract
   * @param ParameterIndex[]  $parameters
   * @param string[] $returnTypes
   */
  public function __construct(SourcePosition $position, $name, $visibility, $final, $static, $abstract, $parameters, $returnTypes) {
    parent::__construct($position, $name, $parameters, $returnTypes);
    $this->final = $final;
    $this->static = $static;
    $this->abstract = $abstract;
    $this->visibility = $visibility;
  }

  /**
   * Whether or not this method is final.
   *
   * @return boolean
   */
  public function isFinal() {
    return $this->final;
  }

  /**
   * Whether or not this method is static.
   *
   * @return boolean
   */
  public function isStatic() {
    return $this->static;
  }

  /**
   * Whether or not this method is abstract.
   *
   * @return boolean
   */
  public function isAbstract() {
    return $this->abstract;
  }

}
