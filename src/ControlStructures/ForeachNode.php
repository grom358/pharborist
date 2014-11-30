<?php
namespace Pharborist\ControlStructures;

use Pharborist\Node;
use Pharborist\ParenTrait;
use Pharborist\StatementNode;

/**
 * foreach control structure.
 */
class ForeachNode extends StatementNode {
  use ParenTrait;
  use AltSyntaxTrait;

  /**
   * @var Node
   */
  protected $onEach;

  /**
   * @var Node
   */
  protected $key;

  /**
   * @var Node
   */
  protected $value;

  /**
   * @var Node
   */
  protected $body;

  /**
   * @return Node
   */
  public function getOnEach() {
    return $this->onEach;
  }

  /**
   * @return Node
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->body;
  }
}
