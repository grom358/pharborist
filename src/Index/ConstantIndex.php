<?php
namespace Pharborist\Index;

class ConstantIndex extends BaseIndex {

  /**
   * @var string
   */
  private $owner;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string|NULL $owner
   */
  public function __construct(FilePosition $position, $name, $owner = NULL) {
    parent::__construct($position, $name);
  }

  /**
   * Get the fully qualified name of class/interface that owns this constant.
   *
   * @return string
   */
  public function getOwner() {
    return $this->owner;
  }

}
