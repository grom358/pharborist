<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ClassStatementNode {
  use FunctionTrait;

  /**
   * @var ModifiersNode
   */
  protected $modifiers;

  /**
   * @return ModifiersNode
   */
  public function getModifiers() {
    return $this->modifiers;
  }
}
