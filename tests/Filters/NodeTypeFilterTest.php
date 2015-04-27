<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Objects\ClassNode;

class NodeTypeFilterTest extends \PHPUnit_Framework_TestCase {


  public function testPass() {
    $this->assertTrue((new NodeTypeFilter(['Pharborist\Functions\FunctionDeclarationNode']))->__invoke(FunctionDeclarationNode::create('foo')));
  }

  public function testFail() {
    $this->assertFalse((new NodeTypeFilter(['Pharborist\Objects\ClassNode']))->__invoke(FunctionDeclarationNode::create('foo')));
  }

  public function testNegatedPass() {
    $this->assertTrue((new NodeTypeFilter(['Pharborist\Functions\FunctionDeclarationNode']))->not()->__invoke(ClassNode::create('foo')));
  }

  public function testNegatedFail() {
    $this->assertFalse((new NodeTypeFilter(['Pharborist\Objects\ClassNode']))->not()->__invoke(ClassNode::create('foo')));
  }

}
