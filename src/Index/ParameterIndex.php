<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

class ParameterIndex extends BaseIndex {

  /**
   * @var string[]
   */
  protected $types;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param string[] $types
   */
  public function __construct($position, $name, $types) {
    $this->sourcePosition = $position;
    $this->name = $name;
    $this->types = $types;
  }

  /**
   * Get the types of the parameter.
   *
   * @return string[]
   */
  public function getTypes() {
    return $this->types;
  }
}
