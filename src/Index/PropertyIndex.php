<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class PropertyIndex extends BaseIndex {

  /**
   * @var string
   */
  private $visibility;

  /**
   * @var string[]
   */
  private $types;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param string $visibility
   * @param string[] $types
   */
  public function __construct($position, $name, $visibility = 'public', $types = ['mixed']) {
    $this->sourcePosition = $position;
    $this->name = $name;
    $this->visibility = $visibility;
    $this->types = $types;
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
