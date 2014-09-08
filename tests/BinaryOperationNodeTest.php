<?php

namespace Pharborist;

class BinaryOperationNodeTest extends \PHPUnit_Framework_TestCase {
  private $op;

  public function __construct() {
    $this->op = Parser::parseSnippet('$doAliensExist = TRUE;')->firstChild();
  }

  public function testInstanceOf() {
    $this->assertInstanceOf('Pharborist\Operator\AssignNode', $this->op);
  }

  public function testGetLeftOperand() {
    $this->assertInstanceOf('Pharborist\VariableNode', $this->op->getLeftOperand());
    $this->assertEquals('doAliensExist', $this->op->getLeftOperand()->getName());
  }

  public function testGetOperator() {
    $this->assertInstanceOf('Pharborist\TokenNode', $this->op->getOperator());
    $this->assertEquals('=', $this->op->getOperator()->getText());
  }

  public function testGetRightOperand() {
    $this->assertInstanceOf('Pharborist\ConstantNode', $this->op->getRightOperand());
    $this->assertEquals('TRUE', $this->op->getRightOperand()->getText());
  }
}
