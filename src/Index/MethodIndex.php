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
   * @param bool $final
   * @param bool $static
   * @param bool $abstract
   * @param ParameterIndex[]  $parameters
   * @param bool $hasReturnTypes
   * @param string[] $returnTypes
   */
  public function __construct(FilePosition $position, $name, $owner, $visibility, $final, $static, $abstract, $parameters, $hasReturnTypes, $returnTypes) {
    parent::__construct($position, $name, $parameters, $hasReturnTypes, $returnTypes);
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
      $this->getVisibility() === $methodIndex->getVisibility();
    if (!$compatible) {
      return FALSE;
    }
    $parameters = $this->getParameters();
    $parameterCount = count($parameters);
    $otherParameters = $methodIndex->getParameters();
    $otherParameterCount = count($otherParameters);
    if ($parameterCount < $otherParameterCount) {
      return FALSE;
    }
    foreach ($otherParameters as $i => $otherParameter) {
      if ($otherParameter->getTypeHint() !== $parameters[$i]->getTypeHint()) {
        return FALSE;
      }
    }
    if ($parameterCount > $otherParameterCount) {
      for ($i = $otherParameterCount; $i < $parameterCount; $i++) {
        if ($parameters[$i]->getDefaultValue() === NULL) {
          return FALSE;
        }
      }
    }
    return TRUE;
  }

}
