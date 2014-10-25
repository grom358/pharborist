<?php

namespace Pharborist;

use Pharborist\ControlStructures\ReturnStatementNode;
use Pharborist\Types\TrueNode;

class ReturnStatementNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $ret = ReturnStatementNode::create(TrueNode::create());
    $this->assertEquals('return TRUE;', $ret->getText());
    $this->assertInstanceOf('\Pharborist\Types\TrueNode', $ret->getExpression());
  }
}
