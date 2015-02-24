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
   * @param SourcePosition $position
   * @param string $name
   */
  public function __construct(SourcePosition $position, $name) {
    $this->sourcePosition = $position;
    $this->name = $name;
  }

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
