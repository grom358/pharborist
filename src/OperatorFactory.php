<?php
namespace Pharborist;

/**
 * Factory for building OperatorNode and also creating Operator for use in
 * ExpressionParser.
 * @package Pharborist
 */
class OperatorFactory {
  /**
   * Associativity, Precedence, Binary Operator, Unary Operator, Static
   * @var array
   */
  private static $operators = array(
    T_LOGICAL_OR => array(Operator::ASSOC_LEFT, 1, TRUE, FALSE, TRUE),
    T_LOGICAL_XOR => array(Operator::ASSOC_LEFT, 2, TRUE, FALSE, TRUE),
    T_LOGICAL_AND => array(Operator::ASSOC_LEFT, 3, TRUE, FALSE, TRUE),
    '=' => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_AND_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_CONCAT_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_DIV_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MINUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MOD_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_MUL_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_OR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_PLUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_SL_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_SR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_XOR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    T_POW_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE),
    '?' => array(Operator::ASSOC_LEFT, 5, FALSE, FALSE, TRUE),
    T_BOOLEAN_OR => array(Operator::ASSOC_LEFT, 6, TRUE, FALSE, TRUE),
    T_BOOLEAN_AND => array(Operator::ASSOC_LEFT, 7, TRUE, FALSE, TRUE),
    '|' => array(Operator::ASSOC_LEFT, 8, TRUE, FALSE, TRUE),
    '^' => array(Operator::ASSOC_LEFT, 9, TRUE, FALSE, TRUE),
    '&' => array(Operator::ASSOC_LEFT, 10, TRUE, FALSE, TRUE),
    T_IS_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_NOT_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    T_IS_NOT_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE),
    '<' => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_IS_SMALLER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_IS_GREATER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    '>' => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE),
    T_SL => array(Operator::ASSOC_LEFT, 13, TRUE, FALSE, TRUE),
    T_SR => array(Operator::ASSOC_LEFT, 13, TRUE, FALSE, TRUE),
    '+' => array(Operator::ASSOC_LEFT, 14, TRUE, TRUE, TRUE),
    '-' => array(Operator::ASSOC_LEFT, 14, TRUE, TRUE, TRUE),
    '.' => array(Operator::ASSOC_LEFT, 14, TRUE, FALSE, TRUE),
    '*' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '/' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '%' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE),
    '!' => array(Operator::ASSOC_RIGHT, 16, FALSE, TRUE, TRUE),
    T_INSTANCEOF => array(Operator::ASSOC_NONE, 17, TRUE, FALSE, FALSE),
    T_INC => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_DEC => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_BOOL_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_INT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_DOUBLE_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_STRING_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_ARRAY_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_OBJECT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    T_UNSET_CAST  => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    '@' => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE),
    '~' => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, TRUE),
    T_POW => array(Operator::ASSOC_RIGHT, 19, TRUE, FALSE, TRUE),
    T_CLONE => array(Operator::ASSOC_RIGHT, 20, FALSE, TRUE, FALSE),
    T_PRINT => array(Operator::ASSOC_RIGHT, 21, FALSE, TRUE, FALSE),
  );

  /**
   * Create an OperatorNode for the given token type.
   * @param int|string $token_type
   * @param bool $static_only
   * @return Operator
   */
  public static function createOperator($token_type, $static_only = FALSE) {
    if (array_key_exists($token_type, self::$operators)) {
      list($assoc, $precedence, $hasBinaryMode, $hasUnaryMode, $static) = self::$operators[$token_type];
      if ($static_only && !$static) {
        return NULL;
      }
      $operator = new Operator();
      $operator->node = new PartialNode();
      $operator->type = $token_type;
      $operator->associativity = $assoc;
      $operator->precedence = $precedence;
      $operator->hasBinaryMode = $hasBinaryMode;
      $operator->hasUnaryMode = $hasUnaryMode;
      return $operator;
    }
    return NULL;
  }

  /**
   * @param Operator $question_operator
   * @param PartialNode $colon_node
   * @return Operator
   */
  public static function createElvisOperator(Operator $question_operator, PartialNode $colon_node) {
    $operator = new Operator();
    $operator->node = new PartialNode();
    $operator->appendChildren($question_operator->children);
    $operator->appendChildren($colon_node->children);
    $operator->associativity = Operator::ASSOC_LEFT;
    $operator->precedence = 5;
    $operator->hasBinaryMode = TRUE;
    $operator->hasUnaryMode = FALSE;
    return $operator;
  }

  /**
   * @param Operator $operator
   * @param Node $operand
   * @return UnaryOperatorNode
   */
  public static function createUnaryOperatorNode(Operator $operator, Node $operand) {
    $node = new UnaryOperatorNode();
    $node->appendChildren($operator->children);
    $node->operator = $operator->operatorNode;
    $node->operand = $node->appendChild($operand);
    return $node;
  }

  /**
   * @param Node $left
   * @param Operator $operator
   * @param Node $right
   * @return BinaryOperatorNode
   */
  public static function createBinaryOperatorNode(Node $left, Operator $operator, Node $right) {
    $node = new BinaryOperatorNode();
    $node->left = $node->appendChild($left);
    $node->appendChildren($operator->children);
    $node->operator = $operator->operatorNode;
    $node->right = $node->appendChild($right);
    return $node;
  }

  /**
   * @param Node $operand
   * @param Operator $operator
   * @return PostDecrementNode|PostIncrementNode
   * @throws ParserException
   */
  public static function createPostfixOperatorNode(Node $operand, Operator $operator) {
    if ($operator->type === T_DEC) {
      $node = new PostDecrementNode();
    }
    elseif ($operator->type === T_INC) {
      $node = new PostIncrementNode();
    }
    else {
      throw new ParserException($operator->operatorNode->getSourcePosition(), "Invalid postfix operator!");
    }
    $node->operand = $node->appendChild($operand);
    $node->appendChildren($operator->children);
    $node->operator = $operator->operatorNode;
    return $node;
  }

  /**
   * @param Node $condition
   * @param Operator $operator
   * @param Node $then
   * @param Node $colon
   * @param Node $else
   * @return TernaryOperatorNode
   */
  public static function createTernaryOperatorNode(
    Node $condition,
    Operator $operator,
    Node $then,
    Node $colon,
    Node $else
  ) {
    $node = new TernaryOperatorNode();
    $node->condition = $node->appendChild($condition);
    $node->appendChildren($operator->children);
    $node->then = $node->appendChild($then);
    $node->appendChildren($colon->children);
    $node->else = $node->appendChild($else);
    return $node;
  }
}
