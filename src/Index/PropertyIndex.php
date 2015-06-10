<?php
namespace Pharborist\Index;

class PropertyIndex extends BaseIndex {

  /**
   * @var string
   */
  private $owner;

  /**
   * @var string
   */
  private $visibility;

  /**
   * @var string[]
   */
  private $types;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string $owner
   * @param string $visibility
   * @param string[] $types
   */
  public function __construct(FilePosition $position, $name, $owner, $visibility = 'public', $types = ['mixed']) {
    parent::__construct($position, $name);
    $this->owner = $owner;
    $this->visibility = $visibility;
    $this->types = $types;
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

}
