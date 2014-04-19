<?php
namespace Pharborist;

/**
 * A class member.
 */
class ClassMemberNode extends ParentNode {
  /**
   * @var Node
   */
  protected $name;

  /**
   * @var Node
   */
  protected $initialValue;

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getInitialValue() {
    return $this->initialValue;
  }
}
