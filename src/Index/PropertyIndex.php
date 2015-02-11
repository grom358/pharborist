<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class PropertyIndex extends BaseIndex {

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $visibility;

  /**
   * @var string
   */
  private $type;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param string $visibility
   * @param string $type
   */
  public function __construct($position, $name, $visibility = 'public', $type = 'mixed') {
    $this->sourcePosition = $position;
    $this->name = $name;
    $this->visibility = $visibility;
    $this->type = $type;
  }

  /**
   * Get the name of the property.
   *
   * @return string
   *   Name of property.
   */
  public function getName() {
    return $this->name;
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

}
