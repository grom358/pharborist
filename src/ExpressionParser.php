<?php
namespace Pharborist;

/**
 * Parse expression using the shunting yard algorithm.
 * @package Pharborist
 */
class ExpressionParser {
  /**
   * @var Operator[]
   */
  private $operators = array();

  /**
   * @var Node[]
   */
  private $operands = array();

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
   * @param Node[] $nodes array of operands and operators
   * @return Node
   * @throws ParserException
   */
  public function parse($nodes) {
    $this->nodes = $nodes;
    $this->position = 0;
    $this->length = count($nodes);
    $this->operators = array($this->sentinel);
    $this->operands = array();
    $this->E();
    if ($this->next()) {
      throw new ParserException($this->next()->getSourcePosition(), "invalid expression");
    }
    return self::arrayLast($this->operands);
  }

  private function E() {
    $this->P();
    while (($node = $this->next()) && ($node instanceof Operator)) {
      // Special case ternary operator
      if ($node->type === '?') {
        $this->expect('?');
        $next = $this->next();
        if ($next && $next->type === ':') {
          // Elvis operator
          $colon = $this->expect(':');
          $elvis_operator = OperatorFactory::createElvisOperator($node, $colon);
          $this->pushOperator($elvis_operator, Operator::MODE_BINARY);
          $this->P();
        } else {
          $this->operators[] = $this->sentinel;
          $this->E();
          array_pop($this->operators);
          $node->colon = $this->expect(':');
          $node->then = array_pop($this->operands);
          $this->pushOperator($node, Operator::MODE_BINARY);
          $this->P();
        }
      }
      // Special case: post T_INC and T_DEC
      elseif ($node->type == T_INC || $node->type == T_DEC) {
        $this->consume();
        $operand = array_pop($this->operands);
        $this->operands[] = OperatorFactory::createPostfixOperatorNode($operand, $node);
      }
      elseif ($node->hasBinaryMode) {
        $this->pushOperator($node, Operator::MODE_BINARY);
        $this->consume();
        $this->P();
      }
      else {
        throw new ParserException($node->getSourcePosition(), "invalid expression");
      }
    }
    while (self::arrayLast($this->operators) !== $this->sentinel) {
      $this->popOperator();
    }
  }

  private function P() {
    $node = $this->next();
    $last = self::arrayLast($this->operators);
    if ($node->type === '&' && $last->type === '=') {
      // reference assignment
      $node->associativity = Operator::ASSOC_RIGHT;
      $node->precedence = self::arrayLast($this->operators)->precedence;
      $this->pushOperator($node, Operator::MODE_UNARY);
      $this->consume();
      $this->P();
    }
    elseif ($node instanceof Operator && $node->hasUnaryMode) {
      $this->pushOperator($node, Operator::MODE_UNARY);
      $this->consume();
      $this->P();
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

  private function operatorCompare($a, $b) {
    if ($a === $this->sentinel) {
      return FALSE;
    }
    if ($a->mode === Operator::MODE_BINARY && $b->mode === Operator::MODE_BINARY) {
      if ($a->precedence > $b->precedence) return TRUE;
      if ($a->associativity === Operator::ASSOC_LEFT && $a->precedence === $b->precedence) return TRUE;
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

  /**
   * @param $expected_type
   * @return Operator
   * @throws ParserException
   */
  private function expect($expected_type) {
    $node = $this->next();
    if ($node === NULL) {
      throw new ParserException($this->nodes[$this->length - 1]->getSourcePosition(), "expected " . $expected_type);
    }
    if ($node->type !== $expected_type) {
      throw new ParserException($node->getSourcePosition(), "expected " . $expected_type);
    }
    $this->consume();
    return $node;
  }
}
