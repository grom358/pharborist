<?php
namespace Pharborist;

/**
 * Factory for building OperatorNode and also creating Operator for use in
 * ExpressionParser.
 * @package Pharborist
 */
class OperatorFactory {
  /**
   * Associativity, Precedence, Binary Operator, Unary Operator, Static, Class Name
   * @var array
   */
  private static $operators = array(
    T_LOGICAL_OR => array(Operator::ASSOC_LEFT, 1, TRUE, FALSE, TRUE, '\Pharborist\LogicalOrNode'),
    T_LOGICAL_XOR => array(Operator::ASSOC_LEFT, 2, TRUE, FALSE, TRUE, '\Pharborist\LogicalXorNode'),
    T_LOGICAL_AND => array(Operator::ASSOC_LEFT, 3, TRUE, FALSE, TRUE, '\Pharborist\LogicalAndNode'),
    '=' => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\AssignNode'),
    T_AND_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\BitwiseAndAssignNode'),
    T_CONCAT_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\ConcatAssignNode'),
    T_DIV_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\DivideAssignNode'),
    T_MINUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\SubtractAssignNode'),
    T_MOD_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\ModulusAssignNode'),
    T_MUL_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\MultiplyAssignNode'),
    T_OR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\BitwiseOrAssignNode'),
    T_PLUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\AddAssignNode'),
    T_SL_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\BitwiseShiftLeftAssignNode'),
    T_SR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\BitwiseShiftRightAssignNode'),
    T_XOR_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\BitwiseXorAssignNode'),
    T_POW_EQUAL => array(Operator::ASSOC_RIGHT, 4, TRUE, FALSE, FALSE, '\Pharborist\PowerAssignNode'),
    '?' => array(Operator::ASSOC_LEFT, 5, FALSE, FALSE, TRUE, '\Pharborist\TernaryOperatorNode'),
    T_BOOLEAN_OR => array(Operator::ASSOC_LEFT, 6, TRUE, FALSE, TRUE, '\Pharborist\BooleanOrNode'),
    T_BOOLEAN_AND => array(Operator::ASSOC_LEFT, 7, TRUE, FALSE, TRUE, '\Pharborist\BooleanAndNode'),
    '|' => array(Operator::ASSOC_LEFT, 8, TRUE, FALSE, TRUE, '\Pharborist\BitwiseOrNode'),
    '^' => array(Operator::ASSOC_LEFT, 9, TRUE, FALSE, TRUE, '\Pharborist\BitwiseXorNode'),
    '&' => array(Operator::ASSOC_LEFT, 10, TRUE, FALSE, TRUE, '\Pharborist\BitwiseAndNode'),
    T_IS_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE, '\Pharborist\IsEqualNode'),
    T_IS_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE, '\Pharborist\IsIdenticalNode'),
    T_IS_NOT_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE, '\Pharborist\IsNotEqualNode'),
    T_IS_NOT_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, FALSE, TRUE, '\Pharborist\IsNotIdenticalNode'),
    '<' => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE, '\Pharborist\LessThanNode'),
    T_IS_SMALLER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE, '\Pharborist\LessThanOrEqualToNode'),
    T_IS_GREATER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE, '\Pharborist\GreaterThanOrEqualToNode'),
    '>' => array(Operator::ASSOC_NONE, 12, TRUE, FALSE, TRUE, '\Pharborist\GreaterThanNode'),
    T_SL => array(Operator::ASSOC_LEFT, 13, TRUE, FALSE, TRUE, '\Pharborist\BitwiseShiftLeftNode'),
    T_SR => array(Operator::ASSOC_LEFT, 13, TRUE, FALSE, TRUE, '\Pharborist\BitwiseShiftRightNode'),
    '+' => array(Operator::ASSOC_LEFT, 14, TRUE, TRUE, TRUE, '\Pharborist\AddNode'),
    '-' => array(Operator::ASSOC_LEFT, 14, TRUE, TRUE, TRUE, '\Pharborist\SubtractNode'),
    '.' => array(Operator::ASSOC_LEFT, 14, TRUE, FALSE, TRUE, '\Pharborist\ConcatNode'),
    '*' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE, '\Pharborist\MultiplyNode'),
    '/' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE, '\Pharborist\DivideNode'),
    '%' => array(Operator::ASSOC_LEFT, 15, TRUE, FALSE, TRUE, '\Pharborist\ModulusNode'),
    '!' => array(Operator::ASSOC_RIGHT, 16, FALSE, TRUE, TRUE, '\Pharborist\BooleanNotNode'),
    T_INSTANCEOF => array(Operator::ASSOC_NONE, 17, TRUE, FALSE, FALSE, '\Pharborist\InstanceOfNode'),
    T_INC => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\PreIncrementNode'),
    T_DEC => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\PreDecrementNode'),
    T_BOOL_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\BooleanCastNode'),
    T_INT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\IntegerCastNode'),
    T_DOUBLE_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\FloatCastNode'),
    T_STRING_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\StringCastNode'),
    T_ARRAY_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\ArrayCastNode'),
    T_OBJECT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\ObjectCastNode'),
    T_UNSET_CAST  => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\UnsetCastNode'),
    '@' => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, FALSE, '\Pharborist\SuppressWarningNode'),
    '~' => array(Operator::ASSOC_RIGHT, 18, FALSE, TRUE, TRUE, '\Pharborist\BitwiseNotNode'),
    T_POW => array(Operator::ASSOC_RIGHT, 19, TRUE, FALSE, TRUE, '\Pharborist\PowerNode'),
    T_CLONE => array(Operator::ASSOC_RIGHT, 20, FALSE, TRUE, FALSE, '\Pharborist\CloneNode'),
    T_PRINT => array(Operator::ASSOC_RIGHT, 21, FALSE, TRUE, FALSE, '\Pharborist\PrintNode'),
  );

  /**
   * Create an OperatorNode for the given token type.
   * @param int|string $token_type
   * @param bool $static_only
   * @return Operator
   */
  public static function createOperator($token_type, $static_only = FALSE) {
    if (array_key_exists($token_type, self::$operators)) {
      list($assoc, $precedence, $hasBinaryMode, $hasUnaryMode, $static, $class_name) = self::$operators[$token_type];
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
      $operator->className = $class_name;
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
    $operator->className = '\Pharborist\ElvisNode';
    return $operator;
  }

  /**
   * @param Operator $operator
   * @param Node $operand
   * @return UnaryOperationNode
   */
  public static function createUnaryOperatorNode(Operator $operator, Node $operand) {
    $class_name = $operator->className;
    /** @var \Pharborist\UnaryOperationNode $node */
    $node = new $class_name();
    $node->appendChildren($operator->children);
    $node->operator = $operator->operatorNode;
    $node->operand = $node->appendChild($operand);
    return $node;
  }

  /**
   * @param Node $left
   * @param Operator $operator
   * @param Node $right
   * @return BinaryOperationNode
   */
  public static function createBinaryOperatorNode(Node $left, Operator $operator, Node $right) {
    $class_name = $operator->className;
    /** @var \Pharborist\BinaryOperationNode $node */
    $node = new $class_name();
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
   * @return TernaryOperationNode
   */
  public static function createTernaryOperatorNode(
    Node $condition,
    Operator $operator,
    Node $then,
    Node $colon,
    Node $else
  ) {
    $node = new TernaryOperationNode();
    $node->condition = $node->appendChild($condition);
    $node->appendChildren($operator->children);
    $node->then = $node->appendChild($then);
    $node->appendChildren($colon->children);
    $node->else = $node->appendChild($else);
    return $node;
  }
}
