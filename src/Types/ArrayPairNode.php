<?php
namespace Pharborist\Types;

use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\Token;

/**
 * A key/value pair element in php array.
 */
class ArrayPairNode extends ParentNode implements ArrayElementNode {
  /**
   * @var Node
   */
  protected $key;

  /**
   * @var Node
   */
  protected $value;

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
   * @param Node $key
   *   Array element's key.
   * @param Node $value
   *   Array element's value.
   *
   * @return ArrayPairNode
   */
  public static function create($key, $value) {
    $node = new ArrayPairNode();
    $node->addChild($key, 'key');
    $node->addChild(Token::space());
    $node->addChild(Token::doubleArrow());
    $node->addChild(Token::space());
    $node->addChild($value, 'value');
    return $node;
  }
}
