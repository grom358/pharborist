<?php
namespace Pharborist\Index;

class ParameterIndex extends BaseIndex {

  /**
   * @var string[]
   */
  protected $types;

  /**
   * @var string
   */
  protected $typeHint;

  /**
   * @var string
   */
  protected $defaultValue;

  /**
   * @var bool
   */
  protected $reference;

  /**
   * @var bool
   */
  protected $variadic;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string[] $types
   * @param string $typeHint
   * @param string $defaultValue
   * @param bool $reference
   * @param bool $variadic
   */
  public function __construct(FilePosition $position, $name, $types, $typeHint, $defaultValue, $reference, $variadic) {
    parent::__construct($position, $name);
    $this->types = $types;
    $this->defaultValue = $defaultValue;
    $this->reference = $reference;
    $this->variadic = $variadic;
  }

  /**
   * Get the types of the parameter.
   *
   * @return string[]
   */
  public function getTypes() {
    return $this->types;
  }

  /**
   * Get the type hint on the parameter.
   *
   * @return string
   */
  public function getTypeHint() {
    return $this->typeHint;
  }

  /**
   * PHP expression of parameter default value.
   *
   * @return string
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }

  /**
   * Return TRUE if parameter is passed by reference.
   *
   * @return bool
   */
  public function isReference() {
    return $this->reference;
  }

  /**
   * Return TRUE if parameter is variadic.
   *
   * @return bool
   */
  public function isVariadic() {
    return $this->variadic;
  }

}
