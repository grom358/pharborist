<?php
namespace Pharborist\ControlStructures;

use Pharborist\ParentNode;
use Pharborist\ExpressionNode;
use Pharborist\TokenNode;

/**
 * A declare directive.
 */
class DeclareDirectiveNode extends ParentNode {
  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ExpressionNode
   */
  protected $value;

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return ExpressionNode
   */
  public function getValue() {
    return $this->value;
  }
}
