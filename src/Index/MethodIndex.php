<?php
namespace Pharborist\Index;

class MethodIndex extends FunctionIndex {

  /**
   * @var string
   */
  private $owner;

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
   * @param FilePosition $position
   * @param string $name
   * @param string $owner
   * @param string $visibility
   * @param boolean $final
   * @param boolean $static
   * @param boolean $abstract
   * @param ParameterIndex[]  $parameters
   * @param string[] $returnTypes
   */
  public function __construct(FilePosition $position, $name, $owner, $visibility, $final, $static, $abstract, $parameters, $returnTypes) {
    parent::__construct($position, $name, $parameters, $returnTypes);
    $this->owner = $owner;
    $this->final = $final;
    $this->static = $static;
    $this->abstract = $abstract;
    $this->visibility = $visibility;
  }

  /**
   * Get the fully qualified name of declaration for method.
   *
   * @return string
   */
  public function getOwner() {
    return $this->owner;
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
   * Test if method definitions are compatible.
   *
   * @param MethodIndex $methodIndex
   *
   * @return bool
   */
  public function compatibleWith(MethodIndex $methodIndex) {
    $compatible = $this->getName() === $methodIndex->getName() &&
      $this->isStatic() === $methodIndex->isStatic() &&
      $this->getVisibility() === $methodIndex->getVisibility() &&
      count($this->parameters) === count($methodIndex->parameters);
    if ($compatible) {
      foreach ($this->parameters as $i => $parameter) {
        if ($parameter->getTypes() !== $methodIndex->parameters[$i]->getTypes()) {
          return FALSE;
        }
      }
    }
    return $compatible;
  }

}
