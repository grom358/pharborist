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
  protected $value;

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->value;
  }
}
