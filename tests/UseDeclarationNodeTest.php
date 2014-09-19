<?php

namespace Pharborist;

class UseDeclarationNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $use = UseDeclarationNode::create('ClassX as ClassY');
    $this->assertInstanceOf('\Pharborist\UseDeclarationNode', $use);
  }
}
