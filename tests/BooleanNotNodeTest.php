<?php
namespace Pharborist;

use Pharborist\Operators\BooleanNotNode;

class BooleanNotNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $expr = Parser::parseExpression('empty($foo)');
    $not = BooleanNotNode::fromExpression($expr);
    $this->assertInstanceOf('\Pharborist\Operators\BooleanNotNode', $not);
    $this->assertSame($expr, $not->getOperand());
    $this->assertEquals('!empty($foo)', $not->getText());
  }
}
