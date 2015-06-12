<?php
namespace Pharborist\Index;

class PropertyIndex extends BaseIndex {

  /**
   * @var string
   */
  private $owner;

  /**
   * @var bool
   */
  private $static;

  /**
   * @var string
   */
  private $visibility;

  /**
   * @var string[]
   */
  private $types;

  /**
   * @var string
   */
  protected $defaultValue;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string $owner
   * @param bool $static
   * @param string $visibility
   * @param string[] $types
   * @param string $defaultValue
   */
  public function __construct(FilePosition $position, $name, $owner, $static, $visibility = 'public', $types = ['mixed'], $defaultValue) {
    parent::__construct($position, $name);
    $this->owner = $owner;
    $this->static = $static;
    $this->visibility = $visibility;
    $this->types = $types;
    $this->defaultValue = $defaultValue;
  }

  /**
   * Get the fully qualified name of class/trait that owns this property.
   *
   * @return string
   */
  public function getOwner() {
    return $this->owner;
  }

  /**
   * Whether or not this property is static.
   *
   * @return bool
   */
  public function isStatic() {
    return $this->static;
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
   * Get property types.
   *
   * @return string[]
   */
  public function getTypes() {
    return $this->types;
  }

  /**
   * PHP expression of default value for property.
   *
   * @return string
   */
  public function getDefaultValue() {
    return $this->defaultValue;
  }

  /**
   * Test if property definitions are compatible.
   *
   * @param PropertyIndex $propertyIndex
   *
   * @return bool
   */
  public function compatibleWith(PropertyIndex $propertyIndex) {
    return $this->getName() === $propertyIndex->getName() &&
      $this->isStatic() === $propertyIndex->isStatic() &&
      $this->getVisibility() === $propertyIndex->getVisibility();
  }
}
