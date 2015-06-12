<?php
namespace Pharborist\Index;

class ConstantIndex extends BaseIndex {

  /**
   * @var string
   */
  private $value;

  /**
   * @var string
   */
  private $owner;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string $value
   * @param string|NULL $owner
   */
  public function __construct(FilePosition $position, $name, $value, $owner = NULL) {
    parent::__construct($position, $name);
    $this->value = $value;
    $this->owner = $owner;
  }

  /**
   * Get the fully qualified name of class/interface that owns this constant.
   *
   * @return string
   */
  public function getOwner() {
    return $this->owner;
  }

  /**
   * PHP scalar expression of constant value.
   *
   * @return string
   */
  public function getValue() {
    return $this->value;
  }
}
