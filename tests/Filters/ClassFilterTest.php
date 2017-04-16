<?php

namespace Pharborist\Filters;

use Pharborist\Objects\ClassNode;
use Pharborist\Token;

class ClassFilterTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var FilterInterface
   */
  private $filter;

  public function setUp() {
    $this->filter = new SingleNodeFilter('Pharborist\Objects\ClassNode', ['Foobaz']);
  }

  public function testPass() {
    $this->assertTrue($this->filter->__invoke(ClassNode::create('Foobaz')));
  }

  public function testFail() {
    $this->assertFalse($this->filter->__invoke(ClassNode::create('Blorf')));
  }

  public function testTypeFail() {
    $this->assertFalse($this->filter->__invoke(Token::_abstract()));
  }

}
