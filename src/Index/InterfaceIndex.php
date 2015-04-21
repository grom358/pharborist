<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

/**
 * Index information about an interface
 */
class InterfaceIndex extends BaseIndex {

  /**
   * @var ClassIndex[]
   */
  private $classes;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param ClassIndex[] $classes
   */
  public function __construct(SourcePosition $position, $name, array $classes = []) {
    parent::__construct($position, $name);
    $this->classes = [];
  }

  /**
   * Get classes which implement this interface.
   *
   * @return ClassIndex[]
   */
  public function getClasses() {
    return $this->classes;
  }

}
