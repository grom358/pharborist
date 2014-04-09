<?php
namespace Pharborist;

/**
 * A class member.
 */
class ClassMemberNode extends ParentNode {
  protected $properties = array(
    'name' => NULL,
    'initialValue' => NULL,
  );

  /**
   * @return Node
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return Node
   */
  public function getInitialValue() {
    return $this->properties['initialValue'];
  }
}
