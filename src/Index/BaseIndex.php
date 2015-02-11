<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

abstract class BaseIndex {

  /**
   * @var SourcePosition
   */
  protected $sourcePosition;

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    return $this->sourcePosition;
  }

}
