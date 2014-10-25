<?php
namespace Pharborist\Constants;

use Pharborist\ParentNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\ExpressionNode;

/**
 * A constant lookup, like `MyNamespace\MY_CONST` or `IS_FUBAR`.
 */
class ConstantNode extends ParentNode implements ExpressionNode {
  /**
   * @param string|NameNode $name
   *
   * @return static
   */
  public static function create($name) {
    if (is_string($name)) {
      $name = NameNode::create($name);
    }
    /** @var ConstantNode $node */
    $node = new static();
    $node->addChild($name, 'constantName');
    return $node;
  }

  /**
   * @var \Pharborist\Namespaces\NameNode
   */
  protected $constantName;

  /**
   * @return \Pharborist\Namespaces\NameNode
   */
  public function getConstantName() {
    return $this->constantName;
  }

  /**
   * Convert the constant into uppercase.
   *
   * @return $this
   */
  public function toUpperCase() {
    $token = $this->getConstantName()->lastToken();
    $token->setText(strtoupper($token->getText()));
    return $this;
  }

  /**
   * Convert the constant into lowercase.
   *
   * @return $this
   */
  public function toLowerCase() {
    $token = $this->getConstantName()->lastToken();
    $token->setText(strtolower($token->getText()));
    return $this;
  }
}
