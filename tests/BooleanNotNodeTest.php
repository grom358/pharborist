<?php
namespace Pharborist;

use Pharborist\Operator\BooleanNotNode;

class BooleanNotNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $expr = Parser::parseExpression('empty($foo)');
    $not = BooleanNotNode::fromExpression($expr);
    $this->assertInstanceOf('\Pharborist\Operator\BooleanNotNode', $not);
    $this->assertSame($expr, $not->getOperand());
    $this->assertEquals('!empty($foo)', $not->getText());
  }
}
