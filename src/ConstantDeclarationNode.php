<?php
namespace Pharborist;

/**
 * Constant declaration.
 */
class ConstantDeclarationNode extends ParentNode {
  protected $properties = array(
    'name' => NULL,
    'value' => NULL,
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
  public function getValue() {
    return $this->properties['value'];
  }
}
