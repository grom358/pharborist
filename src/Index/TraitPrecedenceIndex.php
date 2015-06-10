<?php
namespace Pharborist\Index;

class TraitPrecedenceIndex extends BaseIndex {

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
   * @param string $method_reference
   * @param string $owner_trait
   * @param string $method_name
   * @param string[] $traits
   */
  public function __construct(FilePosition $position, $method_reference, $owner_trait, $method_name, $traits) {
    parent::__construct($position, $method_reference);
    $this->ownerTrait = $owner_trait;
    $this->methodName = $method_name;
    $this->traits = $traits;
  }

  /**
   * Get the fully qualified method name.
   *
   * @return string
   */
  public function getMethodReference() {
    return $this->name;
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
