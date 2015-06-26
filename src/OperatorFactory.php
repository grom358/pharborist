<?php
namespace Pharborist;

use Pharborist\Operators\BinaryOperationNode;
use Pharborist\Operators\PostDecrementNode;
use Pharborist\Operators\PostIncrementNode;
use Pharborist\Operators\TernaryOperationNode;
use Pharborist\Operators\UnaryOperationNode;

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
  private static $operators = [
    T_LOGICAL_OR => [Operator::ASSOC_LEFT, 1, TRUE, '\Pharborist\Operators\LogicalOrNode', NULL],
    T_LOGICAL_XOR => [Operator::ASSOC_LEFT, 2, TRUE, '\Pharborist\Operators\LogicalXorNode', NULL],
    T_LOGICAL_AND => [Operator::ASSOC_LEFT, 3, TRUE, '\Pharborist\Operators\LogicalAndNode', NULL],
    '=' => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\AssignNode', NULL],
    T_AND_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\BitwiseAndAssignNode', NULL],
    T_CONCAT_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\ConcatAssignNode', NULL],
    T_DIV_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\DivideAssignNode', NULL],
    T_MINUS_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\SubtractAssignNode', NULL],
    T_MOD_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\ModulusAssignNode', NULL],
    T_MUL_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\MultiplyAssignNode', NULL],
    T_OR_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\BitwiseOrAssignNode', NULL],
    T_PLUS_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\AddAssignNode', NULL],
    T_SL_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\BitwiseShiftLeftAssignNode', NULL],
    T_SR_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\BitwiseShiftRightAssignNode', NULL],
    T_XOR_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\BitwiseXorAssignNode', NULL],
    T_POW_EQUAL => [Operator::ASSOC_RIGHT, 4, FALSE, '\Pharborist\Operators\PowerAssignNode', NULL],
    '?' => [Operator::ASSOC_LEFT, 5, TRUE, NULL, NULL],
    T_BOOLEAN_OR => [Operator::ASSOC_LEFT, 6, TRUE, '\Pharborist\Operators\BooleanOrNode', NULL],
    T_BOOLEAN_AND => [Operator::ASSOC_LEFT, 7, TRUE, '\Pharborist\Operators\BooleanAndNode', NULL],
    '|' => [Operator::ASSOC_LEFT, 8, TRUE, '\Pharborist\Operators\BitwiseOrNode', NULL],
    '^' => [Operator::ASSOC_LEFT, 9, TRUE, '\Pharborist\Operators\BitwiseXorNode', NULL],
    '&' => [Operator::ASSOC_LEFT, 10, TRUE, '\Pharborist\Operators\BitwiseAndNode', NULL],
    T_IS_EQUAL => [Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\Operators\EqualNode', NULL],
    T_IS_IDENTICAL => [Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\Operators\IdenticalNode', NULL],
    T_IS_NOT_EQUAL => [Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\Operators\NotEqualNode', NULL],
    T_IS_NOT_IDENTICAL => [Operator::ASSOC_NONE, 11, TRUE, '\Pharborist\Operators\NotIdenticalNode', NULL],
    '<' => [Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\Operators\LessThanNode', NULL],
    T_IS_SMALLER_OR_EQUAL => [Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\Operators\LessThanOrEqualToNode', NULL],
    T_IS_GREATER_OR_EQUAL => [Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\Operators\GreaterThanOrEqualToNode', NULL],
    '>' => [Operator::ASSOC_NONE, 12, TRUE, '\Pharborist\Operators\GreaterThanNode', NULL],
    T_SL => [Operator::ASSOC_LEFT, 13, TRUE, '\Pharborist\Operators\BitwiseShiftLeftNode', NULL],
    T_SR => [Operator::ASSOC_LEFT, 13, TRUE, '\Pharborist\Operators\BitwiseShiftRightNode', NULL],
    '+' => [Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\Operators\AddNode', '\Pharborist\Operators\PlusNode'],
    '-' => [Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\Operators\SubtractNode', '\Pharborist\Operators\NegateNode'],
    '.' => [Operator::ASSOC_LEFT, 14, TRUE, '\Pharborist\Operators\ConcatNode', NULL],
    '*' => [Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\Operators\MultiplyNode', NULL],
    '/' => [Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\Operators\DivideNode', NULL],
    '%' => [Operator::ASSOC_LEFT, 15, TRUE, '\Pharborist\Operators\ModulusNode', NULL],
    '!' => [Operator::ASSOC_RIGHT, 16, TRUE, NULL, '\Pharborist\Operators\BooleanNotNode'],
    T_INSTANCEOF => [Operator::ASSOC_NONE, 17, FALSE, '\Pharborist\Operators\InstanceOfNode', NULL],
    T_INC => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\PreIncrementNode'],
    T_DEC => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\PreDecrementNode'],
    T_BOOL_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\BooleanCastNode'],
    T_INT_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\IntegerCastNode'],
    T_DOUBLE_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\FloatCastNode'],
    T_STRING_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\StringCastNode'],
    T_ARRAY_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\ArrayCastNode'],
    T_OBJECT_CAST => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\ObjectCastNode'],
    T_UNSET_CAST  => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\UnsetCastNode'],
    '@' => [Operator::ASSOC_RIGHT, 18, FALSE, NULL, '\Pharborist\Operators\SuppressWarningNode'],
    '~' => [Operator::ASSOC_RIGHT, 18, TRUE, NULL, '\Pharborist\Operators\BitwiseNotNode'],
    T_POW => [Operator::ASSOC_RIGHT, 19, TRUE, '\Pharborist\Operators\PowerNode', NULL],
    T_CLONE => [Operator::ASSOC_RIGHT, 20, FALSE, NULL, '\Pharborist\Operators\CloneNode'],
    T_PRINT => [Operator::ASSOC_RIGHT, 21, FALSE, NULL, '\Pharborist\Operators\PrintNode'],
  ];

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
    $operator->binaryClassName = '\Pharborist\Operators\ElvisNode';
    return $operator;
  }

  /**
   * @param Operator $assign_operator
   * @param PartialNode $by_ref_node
   * @return Operator
   */
  public static function createAssignReferenceOperator(Operator $assign_operator, PartialNode $by_ref_node) {
    $operator = new Operator();
    $operator->mergeNode($assign_operator);
    $operator->mergeNode($by_ref_node);
    $operator->associativity = Operator::ASSOC_RIGHT;
    $operator->precedence = 4;
    $operator->hasBinaryMode = TRUE;
    $operator->hasUnaryMode = FALSE;
    $operator->binaryClassName = '\Pharborist\Operators\AssignReferenceNode';
    return $operator;
  }

  /**
   * @param Operator $operator
   * @param Node $operand
   * @return UnaryOperationNode
   */
  public static function createUnaryOperatorNode(Operator $operator, Node $operand) {
    $class_name = $operator->unaryClassName;
    /** @var UnaryOperationNode $node */
    $node = new $class_name();
    $node->mergeNode($operator);
    $node->addChild($operand, 'operand');
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
    /** @var BinaryOperationNode $node */
    $node = new $class_name();
    $node->addChild($left, 'left');
    $node->mergeNode($operator);
    $node->addChild($right, 'right');
    return $node;
  }

  /**
   * @param Node $operand
   * @param Operator $operator
   * @param string $filename
   * @return PostDecrementNode|PostIncrementNode
   * @throws ParserException
   */
  public static function createPostfixOperatorNode(Node $operand, Operator $operator, $filename = NULL) {
    if ($operator->type === T_DEC) {
      $node = new PostDecrementNode();
    }
    elseif ($operator->type === T_INC) {
      $node = new PostIncrementNode();
    }
    else {
      $op = $operator->getOperator();
      throw new ParserException(
        $filename,
        $op->getLineNumber(),
        $op->getColumnNumber(),
        "Invalid postfix operator!");
    }
    $node->addChild($operand, 'operand');
    $node->mergeNode($operator);
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
    $node->addChild($condition, 'condition');
    $node->mergeNode($operator);
    $node->addChild($then, 'then');
    $node->mergeNode($colon);
    $node->addChild($else, 'else');
    return $node;
  }
}
