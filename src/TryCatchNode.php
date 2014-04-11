<?php
namespace Pharborist;

/**
 * A try control structure.
 */
class TryCatchNode extends StatementNode {
  protected $properties = array(
    'try' => NULL,
    'catches' => array(),
    'finally' => NULL,
  );

  /**
   * @return Node
   */
  public function getTry() {
    return $this->properties['try'];
  }

  /**
   * @return CatchNode[]
   */
  public function getCatches() {
    return $this->properties['catches'];
  }

  /**
   * @return Node
   */
  public function getFinally() {
    return $this->properties['finally'];
  }
}
