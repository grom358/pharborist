<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Token;

class NodeTypeFilterTest extends \PHPUnit_Framework_TestCase {

  public function testPass() {
    $this->assertTrue((new NodeTypeFilter(['Pharborist\Functions\FunctionDeclarationNode']))->__invoke(FunctionDeclarationNode::create('foo')));
  }

  public function testFail() {
    $this->assertFalse((new NodeTypeFilter(['Pharborist\Objects\ClassNode']))->__invoke(FunctionDeclarationNode::create('bang')));
  }

}
