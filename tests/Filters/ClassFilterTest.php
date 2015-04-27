<?php

namespace Pharborist\Filters;

use Pharborist\Objects\ClassNode;
use Pharborist\Token;

class ClassFilterTest extends \PHPUnit_Framework_TestCase {

  public function testPass() {
    $this->assertTrue((new ClassFilter(['Foobaz']))->__invoke(ClassNode::create('Foobaz')));
  }

  public function testFail() {
    $this->assertFalse((new ClassFilter(['Foobaz']))->__invoke(ClassNode::create('Blorf')));
  }

  public function testTypeFail() {
    $this->assertFalse((new ClassFilter(['Foobaz']))->__invoke(Token::_abstract()));
  }

}
