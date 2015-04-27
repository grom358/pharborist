<?php

namespace Pharborist\Filters;

use Pharborist\Objects\ClassMethodCallNode;
use Pharborist\Token;

class ClassMethodCallFilterTest extends \PHPUnit_Framework_TestCase {

  public function testPass() {
    $this->assertTrue((new ClassMethodCallFilter('Foobaz', 'create'))->__invoke(ClassMethodCallNode::create('Foobaz', 'create')));
  }

  public function testFail() {
    $this->assertFalse((new ClassMethodCallFilter('Foobaz', 'create'))->__invoke(ClassMethodCallNode::create('Blorf', 'create')));
  }

  public function testTypeFail() {
    $this->assertFalse((new ClassMethodCallFilter('Foobaz', 'create'))->__invoke(Token::_abstract()));
  }

}
