<?php
namespace Pharborist;

/**
 * A try control structure.
 */
class TryCatchNode extends StatementNode {
  /**
   * @var Node
   */
  protected $try;

  /**
   * @var Node
   */
  protected $finally;

  /**
   * @return Node
   */
  public function getTry() {
    return $this->try;
  }

  /**
   * @return CatchNode[]
   */
  public function getCatches() {
    return $this->childrenByInstance('\Pharborist\CatchNode');
  }

  /**
   * @return Node
   */
  public function getFinally() {
    return $this->finally;
  }
}
