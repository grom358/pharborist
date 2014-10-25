<?php
namespace Pharborist\Exceptions;

use Pharborist\Node;
use Pharborist\StatementNode;

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
    return $this->childrenByInstance('\Pharborist\Exceptions\CatchNode');
  }

  /**
   * @return Node
   */
  public function getFinally() {
    return $this->finally;
  }

  /**
   * Returns if this try/catch has a catch for a certain exception type.
   *
   * @param string $exception ...
   *  At least one exception type to check for. Each should be a fully qualified
   *  name, e.g. `\Exception` instead of `Exception`.
   *
   * @return boolean
   */
  public function catches($exception) {
    $exceptions = func_get_args();

    foreach ($this->getCatches() as $catch) {
      if (in_array($catch->getExceptionType()->getAbsolutePath(), $exceptions, TRUE)) {
        return TRUE;
      }
    }
    return FALSE;
  }
}
