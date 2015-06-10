<?php
namespace Pharborist\Index;

abstract class BaseIndex {

  /**
   * @var FilePosition
   */
  protected $position;

  /**
   * @var string
   */
  protected $name;

  /**
   * @param FilePosition $position
   * @param string $name
   */
  public function __construct(FilePosition $position, $name) {
    $this->position = $position;
    $this->name = $name;
  }

  /**
   * @return FilePosition
   */
  public function getPosition() {
    return $this->position;
  }

  /**
   * Get the name of index item.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

}
