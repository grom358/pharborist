<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

abstract class BaseIndex {

  /**
   * @var SourcePosition
   */
  protected $sourcePosition;

  /**
   * @var string
   */
  protected $name;

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    return $this->sourcePosition;
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
