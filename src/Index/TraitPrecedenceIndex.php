<?php
namespace Pharborist\Index;

class TraitPrecedenceIndex {

  /**
   * @var FilePosition
   */
  protected $position;

  /**
   * @var string
   */
  protected $ownerTrait;

  /**
   * @var string
   */
  protected $methodName;

  /**
   * @var string[]
   */
  protected $traits;

  /**
   * @param FilePosition $position
   * @param string $owner_trait
   * @param string $method_name
   * @param string[] $traits
   */
  public function __construct(FilePosition $position, $owner_trait, $method_name, $traits) {
    $this->position  = $position;
    $this->ownerTrait = $owner_trait;
    $this->methodName = $method_name;
    $this->traits = $traits;
  }

  /**
   * @return FilePosition
   */
  public function getPosition() {
    return $this->position;
  }

  /**
   * Get the fully qualified method name.
   *
   * @return string
   */
  public function getMethodReference() {
    return $this->ownerTrait . '::' . $this->methodName;
  }

  /**
   * Get the fully qualified trait name.
   *
   * @return string
   */
  public function getOwnerTrait() {
    return $this->ownerTrait;
  }

  /**
   * Get the method name.
   *
   * @return string
   */
  public function getMethodName() {
    return $this->methodName;
  }

  /**
   * Get the fully qualified trait names.
   *
   * @return string[]
   */
  public function getTraits() {
    return $this->traits;
  }

}
