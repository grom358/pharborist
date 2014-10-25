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
    $this->assertCount(2, $call->getArguments());
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $call->getArgumentList()->getItem(0));
    $this->assertInstanceof('\Pharborist\Types\StringNode', $call->getArgumentList()->getItem(1));

    $pi = FloatNode::fromValue(3.141);
    $call->appendArgument($pi);
    $this->assertSame($pi, $call->getArgumentList()->getItem(2));
  }

  public function testPrependArgument() {
    $call = Parser::parseExpression('foo()');

    $call->prependArgument('wozwoz');
    $this->assertCount(1, $call->getArguments());
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $call->getArgumentList()->getItem(0));

    $bazbaz = StringNode::fromValue('bazbaz');
    $call->prependArgument($bazbaz);
    $this->assertCount(2, $call->getArguments());
    $this->assertSame($bazbaz, $call->getArgumentList()->getItem(0));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testPrependInvalidArgument() {
    Parser::parseExpression('foo()')->prependArgument(NULL);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testAppendInvalidArgument() {
    Parser::parseExpression('foo()')->appendArgument(NULL);
  }
}
