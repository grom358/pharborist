<?php
namespace Pharborist;

use Pharborist\Types\FloatNode;
use Pharborist\Types\StringNode;

class ArgumentTraitTest extends \PHPUnit_Framework_TestCase {
  public function testAppendArgument() {
    /** @var \Pharborist\Functions\FunctionCallNode $call */
    $call = Parser::parseExpression('foo()');

    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $call);
    $this->assertCount(0, $call->getArguments());

    $call->appendArgument(1)->appendArgument('hohoho');
    $arguments = $call->getArguments();
    $this->assertCount(2, $arguments);
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $arguments[0]);
    $this->assertInstanceof('\Pharborist\Types\StringNode', $arguments[1]);

    $pi = FloatNode::fromValue(3.141);
    $call->appendArgument($pi);
    $this->assertSame($pi, $call->getArguments()->get(2));
  }

  public function testPrependArgument() {
    /** @var \Pharborist\Functions\FunctionCallNode $call */
    $call = Parser::parseExpression('foo()');

    $call->prependArgument('wozwoz');
    $arguments = $call->getArguments();
    $this->assertCount(1, $arguments);
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $arguments[0]);

    $bazbaz = StringNode::fromValue('bazbaz');
    $call->prependArgument($bazbaz);
    $arguments = $call->getArguments();
    $this->assertCount(2, $arguments);
    $this->assertSame($bazbaz, $arguments[0]);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testPrependInvalidArgument() {
    /** @var \Pharborist\Functions\FunctionCallNode $call */
    $call = Parser::parseExpression('foo()');
    $call->prependArgument(NULL);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testAppendInvalidArgument() {
    /** @var \Pharborist\Functions\FunctionCallNode $call */
    $call = Parser::parseExpression('foo()');
    $call->appendArgument(NULL);
  }
}
