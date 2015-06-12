<?php
namespace Pharborist\Index;

class TraitAliasIndex {

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
   * @var string
   */
  protected $aliasVisibility;

  /**
   * @var string
   */
  protected $aliasName;

  /**
   * @param FilePosition $position
   * @param string $owner_trait
   * @param string $method_name
   * @param string $alias_name
   * @param string $alias_visibility
   */
  public function __construct(FilePosition $position, $owner_trait, $method_name, $alias_name, $alias_visibility = NULL) {
    $this->position  = $position;
    $this->ownerTrait = $owner_trait;
    $this->methodName = $method_name;
    $this->aliasName = $alias_name;
    $this->aliasVisibility = $alias_visibility;
  }

  /**
   * @return FilePosition
   */
  public function getPosition() {
    return $this->position;
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
   * Get the aliased method name.
   *
   * @return string
   */
  public function getAliasName() {
    return $this->aliasName;
  }

  /**
   * Get the aliased visibility.
   *
   * @return null|string
   */
  public function getAliasVisibility() {
    return $this->aliasVisibility;
  }

}
