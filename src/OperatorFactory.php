<?php
namespace Pharborist;

/**
 * Factory for creating OperatorNode for use in ExpressionParser.
 * @package Pharborist
 */
class OperatorFactory {
  /**
   * Associativity, Precedence, Binary Operator, Unary Operator, Static
   * @var array
   */
  private static $operators = array(
    T_LOGICAL_OR => array(OperatorNode::ASSOC_LEFT, 1, TRUE, FALSE, TRUE),
    T_LOGICAL_XOR => array(OperatorNode::ASSOC_LEFT, 2, TRUE, FALSE, TRUE),
    T_LOGICAL_AND => array(OperatorNode::ASSOC_LEFT, 3, TRUE, FALSE, TRUE),
    '=' => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_AND_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_CONCAT_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_DIV_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MINUS_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MOD_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MUL_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_OR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_PLUS_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_SL_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_SR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_XOR_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_POW_EQUAL => array(OperatorNode::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    '?' => array(OperatorNode::ASSOC_LEFT, 5, FALSE, FALSE, TRUE),
    T_BOOLEAN_OR => array(OperatorNode::ASSOC_LEFT, 6, TRUE, FALSE, TRUE),
    T_BOOLEAN_AND => array(OperatorNode::ASSOC_LEFT, 7, TRUE, FALSE, TRUE),
    '|' => array(OperatorNode::ASSOC_LEFT, 8, TRUE, FALSE, TRUE),
    '^' => array(OperatorNode::ASSOC_LEFT, 9, TRUE, FALSE, TRUE),
    '&' => array(OperatorNode::ASSOC_LEFT, 10, TRUE, FALSE, TRUE),
    T_IS_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_NOT_EQUAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_NOT_IDENTICAL => array(OperatorNode::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    '<' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_IS_SMALLER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_IS_GREATER_OR_EQUAL => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    '>' => array(OperatorNode::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_SL => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE, TRUE),
    T_SR => array(OperatorNode::ASSOC_LEFT, 13, TRUE, FALSE, TRUE),
    '+' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE, TRUE),
    '-' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, TRUE, TRUE),
    '.' => array(OperatorNode::ASSOC_LEFT, 14, TRUE, FALSE, TRUE),
    '*' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '/' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '%' => array(OperatorNode::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '!' => array(OperatorNode::ASSOC_RIGHT, 16, FALSE, TRUE, TRUE),
    T_INSTANCEOF => array(OperatorNode::ASSOC_NONE, 17, TRUE, FALSE, FALSE),
    T_INC => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_DEC => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_BOOL_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_INT_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_DOUBLE_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_STRING_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_ARRAY_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_OBJECT_CAST => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_UNSET_CAST  => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    '@' => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    '~' => array(OperatorNode::ASSOC_RIGHT, 18, FALSE, TRUE, TRUE),
    T_POW => array(OperatorNode::ASSOC_RIGHT, 19, TRUE, FALSE, TRUE),
    T_CLONE => array(OperatorNode::ASSOC_RIGHT, 20, FALSE, TRUE, FALSE),
    T_PRINT => array(OperatorNode::ASSOC_RIGHT, 21, FALSE, TRUE, FALSE),
  );

  /**
   * Create an OperatorNode for the given token type.
   * @param int|string $token_type
   * @param bool $static_only
   * @return OperatorNode
   */
  public static function createOperator($token_type, $static_only = FALSE) {
    if (array_key_exists($token_type, self::$operators)) {
      list($assoc, $precedence, $hasBinaryMode, $hasUnaryMode, $static) = self::$operators[$token_type];
      if ($static_only && !$static) {
        return NULL;
      }
      $node = new OperatorNode();
      $node->type = $token_type;
      $node->associativity = $assoc;
      $node->precedence = $precedence;
      $node->hasBinaryMode = $hasBinaryMode;
      $node->hasUnaryMode = $hasUnaryMode;
      return $node;
    }
    return NULL;
  }
}
