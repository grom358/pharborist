<?php
namespace Pharborist;

/**
 * An Operator factory.
 *
 * Factory for building OperatorNode and also creating Operator for use in
 * ExpressionParser.
 */
class OperatorFactory {
  /**
   * Associativity, Precedence, Static, Binary Class, Unary Class
   * @var array
   */
  private static $operators = array(
    T_LOGICAL_OR => array(Operator::ASSOC_LEFT, 1, TRUE, '\Pharborist\LogicalOrNode', NULL),
    T_LOGICAL_XOR => array(Operator::ASSOC_LEFT, 2, TRUE, '\Pharborist\LogicalXorNode', NULL),
    T_LOGICAL_AND => array(Operator::ASSOC_LEFT, 3, TRUE, '\Pharborist\LogicalAndNode', NULL),
    '=' => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\AssignNode', NULL),
    T_AND_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\BitwiseAndAssignNode', NULL),
    T_CONCAT_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\ConcatAssignNode', NULL),
    T_DIV_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\DivideAssignNode', NULL),
    T_MINUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\SubtractAssignNode', NULL),
    T_MOD_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\ModulusAssignNode', NULL),
    T_MUL_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\MultiplyAssignNode', NULL),
    T_OR_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\BitwiseOrAssignNode', NULL),
    T_PLUS_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\AddAssignNode', NULL),
    T_SL_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\BitwiseShiftLeftAssignNode', NULL),
    T_SR_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\BitwiseShiftRightAssignNode', NULL),
    T_XOR_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\BitwiseXorAssignNode', NULL),
    T_POW_EQUAL => array(Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\PowerAssignNode', NULL),
    '?' => array(Operator::ASSOC_LEFT, 5, TRUE, NULL, NULL),
    ':' => array(Operator::ASSOC_NONE, 5, TRUE, NULL, NULL),
    T_BOOLEAN_OR => array(Operator::ASSOC_LEFT, 6, TRUE, '\Pharborist\BooleanOrNode', NULL),
    T_BOOLEAN_AND => array(Operator::ASSOC_LEFT, 7, TRUE, '\Pharborist\BooleanAndNode', NULL),
    '|' => array(Operator::ASSOC_LEFT, 8, TRUE, '\Pharborist\BitwiseOrNode', NULL),
    '^' => array(Operator::ASSOC_LEFT, 9, TRUE, '\Pharborist\BitwiseXorNode', NULL),
    '&' => array(Operator::ASSOC_LEFT, 10, TRUE, '\Pharborist\BitwiseAndNode', NULL),
    T_IS_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\EqualNode', NULL),
    T_IS_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\IdenticalNode', NULL),
    T_IS_NOT_EQUAL => array(Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\NotEqualNode', NULL),
    T_IS_NOT_IDENTICAL => array(Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\NotIdenticalNode', NULL),
    '<' => array(Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\LessThanNode', NULL),
    T_IS_SMALLER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\LessThanOrEqualToNode', NULL),
    T_IS_GREATER_OR_EQUAL => array(Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\GreaterThanOrEqualToNode', NULL),
    '>' => array(Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\GreaterThanNode', NULL),
    T_SL => array(Operator::ASSOC_LEFT, 13, TRUE, '\Pharborist\BitwiseShiftLeftNode', NULL),
    T_SR => array(Operator::ASSOC_LEFT, 13, TRUE, '\Pharborist\BitwiseShiftRightNode', NULL),
    '+' => array(Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\AddNode', '\Pharborist\PlusNode'),
    '-' => array(Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\SubtractNode', '\Pharborist\NegateNode'),
    '.' => array(Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\ConcatNode', NULL),
    '*' => array(Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\MultiplyNode', NULL),
    '/' => array(Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\DivideNode', NULL),
    '%' => array(Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\ModulusNode', NULL),
    '!' => array(Operator::ASSOC_RIGHT, 16, TRUE, NULL, '\Pharborist\BooleanNotNode'),
    T_INSTANCEOF => array(Operator::ASSOC_NONE, 17, FALSE, '\Pharborist\InstanceOfNode', NULL),
    T_INC => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\PreIncrementNode'),
    T_DEC => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\PreDecrementNode'),
    T_BOOL_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\BooleanCastNode'),
    T_INT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\IntegerCastNode'),
    T_DOUBLE_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\FloatCastNode'),
    T_STRING_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\StringCastNode'),
    T_ARRAY_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\ArrayCastNode'),
    T_OBJECT_CAST => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\ObjectCastNode'),
    T_UNSET_CAST  => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\UnsetCastNode'),
    '@' => array(Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\SuppressWarningNode'),
    '~' => array(Operator::ASSOC_RIGHT, 18, TRUE, NULL, '\Pharborist\BitwiseNotNode'),
    T_POW => array(Operator::ASSOC_RIGHT, 19, TRUE, '\Pharborist\PowerNode'),
    T_CLONE => array(Operator::ASSOC_RIGHT, 20, FALSE, NULL, '\Pharborist\CloneNode'),
    T_PRINT => array(Operator::ASSOC_RIGHT, 21, FALSE, NULL, '\Pharborist\PrintNode'),
  );

  /**
   * Create an OperatorNode for the given token type.
   * @param int|string $token_type
   * @param bool $static_only
   * @return Operator
   */
  public static function createOperator($token_type, $static_only = FALSE) {
    if (array_key_exists($token_type, self::$operators)) {
      list($assoc, $precedence, $static, $binary_class_name, $unary_class_name) = self::$operators[$token_type];
      if ($static_only && !$static) {
        return NULL;
      }
      $operator = new Operator();
      $operator->type = $token_type;
      $operator->associativity = $assoc;
      $operator->precedence = $precedence;
      $operator->hasBinaryMode = $binary_class_name !== NULL;
      $operator->hasUnaryMode = $unary_class_name !== NULL;
      $operator->binaryClassName = $binary_class_name;
      $operator->unaryClassName = $unary_class_name;
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
    $operator->mergeNode($question_operator);
    $operator->mergeNode($colon_node);
    $operator->type = '?:';
    $operator->associativity = Operator::ASSOC_LEFT;
    $operator->precedence = 5;
    $operator->hasBinaryMode = TRUE;
    $operator->hasUnaryMode = FALSE;
    $operator->binaryClassName = '\Pharborist\ElvisNode';
    return $operator;
  }

  /**
   * @param Operator $assign_operator
   * @param Operator $by_ref_operator
   * @return Operator
   */
  public static function createAssignReferenceOperator(Operator $assign_operator, Operator $by_ref_operator) {
    $operator = new Operator();
    $operator->mergeNode($assign_operator);
    $operator->mergeNode($by_ref_operator);
    $operator->associativity = Operator::ASSOC_RIGHT;
    $operator->precedence = 4;
    $operator->hasBinaryMode = TRUE;
    $operator->hasUnaryMode = FALSE;
    $operator->binaryClassName = '\Pharborist\AssignReferenceNode';
    return $operator;
  }

  /**
   * @param Operator $operator
   * @param Node $operand
   * @return UnaryOperationNode
   */
  public static function createUnaryOperatorNode(Operator $operator, Node $operand) {
    $class_name = $operator->unaryClassName;
    /** @var \Pharborist\UnaryOperationNode $node */
    $node = new $class_name();
    $node->mergeNode($operator);
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
    $class_name = $operator->binaryClassName;
    /** @var \Pharborist\BinaryOperationNode $node */
    $node = new $class_name();
    $node->left = $node->appendChild($left);
    $node->mergeNode($operator);
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
    $node->mergeNode($operator);
    $node->operator = $operator->operatorNode;
    return $node;
  }

  /**
   * @param Node $condition
   * @param Operator $operator
   * @param Node $then
   * @param ParentNode $colon
   * @param Node $else
   * @return TernaryOperationNode
   */
  public static function createTernaryOperatorNode(
    Node $condition,
    Operator $operator,
    Node $then,
    ParentNode $colon,
    Node $else
  ) {
    $node = new TernaryOperationNode();
    $node->condition = $node->appendChild($condition);
    $node->mergeNode($operator);
    $node->then = $node->appendChild($then);
    $node->mergeNode($colon);
    $node->else = $node->appendChild($else);
    return $node;
  }
}
