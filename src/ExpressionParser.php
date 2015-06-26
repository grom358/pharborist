<?php
namespace Pharborist;

/**
 * Parses expressions using the shunting yard algorithm.
 *
 * @internal
 */
class ExpressionParser {
  /**
   * @var string
   */
  private $filename;

  /**
   * @var Operator[]
   */
  private $operators = [];

  /**
   * @var Node[]
   */
  private $operands = [];

  /**
   * @var Operator
   */
  private $sentinel;

  /**
   * @var Node[]
   */
  private $nodes;

  /**
   * @var int
   */
  private $position;

  /**
   * @var int
   */
  private $length;

  /**
   * Constructor.
   */
  public function __construct() {
    $sentinel = new Operator();
    $sentinel->associativity = Operator::ASSOC_NONE;
    $sentinel->precedence = -1;
    $sentinel->type = ';';
    $this->sentinel = $sentinel;
  }

  /**
   * @param $array
   * @return Operator
   */
  static private function arrayLast($array) {
    if (count($array) < 1) {
      return NULL;
    }
    return $array[count($array) - 1];
  }

  /**
   * Parse the expression nodes into a tree.
   * @param Node[] $nodes
   *   Array of operands and operators
   * @param string $filename
   *   Filename being parsed.
   * @return Node
   * @throws ParserException
   */
  public function parse($nodes, $filename = NULL) {
    $this->nodes = $nodes;
    $this->filename = $filename;
    $this->position = 0;
    $this->length = count($nodes);
    $this->operators = [$this->sentinel];
    $this->operands = [];
    $this->E();
    if ($this->next()) {
      $next = $this->next();
      throw new ParserException(
        $this->filename,
        $next->getLineNumber(),
        $next->getColumnNumber(),
        "invalid expression");
    }
    return self::arrayLast($this->operands);
  }

  private function E() {
    $this->P();
    while (($node = $this->next()) && ($node instanceof Operator)) {
      // Special case: post T_INC and T_DEC
      if ($node->type == T_INC || $node->type == T_DEC) {
        $this->consume();
        $operand = array_pop($this->operands);
        $this->operands[] = OperatorFactory::createPostfixOperatorNode($operand, $node, $this->filename);
      }
      elseif ($node->type === '?' || $node->hasBinaryMode) {
        $this->pushOperator($node, Operator::MODE_BINARY);
        $this->consume();
        $this->P();
      }
      else {
        throw new ParserException(
          $this->filename,
          $node->getLineNumber(),
          $node->getColumnNumber(),
          "invalid expression");
      }
    }
    while (self::arrayLast($this->operators) !== $this->sentinel) {
      $this->popOperator();
    }
  }

  private function P() {
    $node = $this->next();
    if ($node instanceof Operator) {
      if ($node->hasUnaryMode) {
        $this->pushOperator($node, Operator::MODE_UNARY);
        $this->consume();
        $this->P();
      }
      else {
        throw new ParserException(
          $this->filename,
          $node->getLineNumber(),
          $node->getColumnNumber(),
          'unexpected ' . $node->getOperator() . ' operator!');
      }
    }
    else {
      $this->operands[] = $node;
      $this->consume();
    }
  }

  private function pushOperator($operator, $mode) {
    $operator->mode = $mode;
    while ($this->operatorCompare(self::arrayLast($this->operators), $operator)) {
      $this->popOperator();
    }
    $this->operators[] = $operator;
  }

  /**
   * @param Operator $a
   * @param Operator $b
   * @return bool
   * @throws ParserException
   */
  private function operatorCompare($a, $b) {
    if ($a === $this->sentinel) {
      return FALSE;
    }
    if ($a->mode === Operator::MODE_BINARY && $b->mode === Operator::MODE_BINARY) {
      if ($a->precedence > $b->precedence) return TRUE;
      if ($a->associativity === Operator::ASSOC_LEFT && $a->precedence === $b->precedence) return TRUE;
      $non_associative = $a->associativity === Operator::ASSOC_NONE && $b->associativity === Operator::ASSOC_NONE;
      if ($non_associative && $a->precedence === $b->precedence) {
        throw new ParserException(
          $this->filename,
          $b->getLineNumber(),
          $b->getColumnNumber(),
          'Non-associative operators of equal precedence can not be next to each other!'
        );
      }
    }
    elseif ($a->mode === Operator::MODE_UNARY && $b->mode === Operator::MODE_BINARY) {
      if ($a->precedence >= $b->precedence) return TRUE;
    }
    return FALSE;
  }

  private function popOperator() {
    $op = array_pop($this->operators);
    if ($op->type === '?') {
      $else = array_pop($this->operands);
      $colon = $op->colon;
      $then = $op->then;
      $condition = array_pop($this->operands);
      $this->operands[] = OperatorFactory::createTernaryOperatorNode(
        $condition,
        $op,
        $then,
        $colon,
        $else
      );
    }
    elseif ($op->mode === Operator::MODE_UNARY) {
      $operand = array_pop($this->operands);
      $this->operands[] = OperatorFactory::createUnaryOperatorNode($op, $operand);
    }
    else {
      $right = array_pop($this->operands);
      $left = array_pop($this->operands);
      $this->operands[] = OperatorFactory::createBinaryOperatorNode($left, $op, $right);
    }
  }

  private function consume() {
    $this->position++;
  }

  /**
   * @return Node
   */
  private function next() {
    if ($this->position >= $this->length) {
      return NULL;
    }
    return $this->nodes[$this->position];
  }
}
