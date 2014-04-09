<?php
namespace Pharborist;

/**
 * A for control structure.
 */
class ForNode extends StatementNode {
  protected $properties = array(
    'initial' => NULL,
    'condition' => NULL,
    'step' => NULL,
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getInitial() {
    return $this->properties['initial'];
  }

  /**
   * @return Node
   */
  public function getCondition() {
    return $this->properties['condition'];
  }

  /**
   * @return Node
   */
  public function getStep() {
    return $this->properties['step'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
