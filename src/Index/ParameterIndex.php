<?php
namespace Pharborist\Index;

class ParameterIndex extends BaseIndex {

  /**
   * @var bool
   */
  protected $hasTypes;

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
   * @param bool $hasTypes
   * @param string[] $types
   * @param string $typeHint
   * @param string $defaultValue
   * @param bool $reference
   * @param bool $variadic
   */
  public function __construct(FilePosition $position, $name, $hasTypes, $types, $typeHint, $defaultValue, $reference, $variadic) {
    parent::__construct($position, $name);
    $this->hasTypes = $hasTypes;
    $this->types = $types;
    $this->defaultValue = $defaultValue;
    $this->reference = $reference;
    $this->variadic = $variadic;
  }

  /**
   * Get whether parameter has phpDoc types.
   *
   * @return bool
   */
  public function hasDocTypes() {
    return $this->hasTypes;
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
   * Set the types of the parameter.
   *
   * @internal Used by Indexer.
   *
   * @param string[] $types
   * @return $this
   */
  public function setTypes(array $types) {
    $this->types = $types;
    $this->hasTypes = TRUE;
    return $this;
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
