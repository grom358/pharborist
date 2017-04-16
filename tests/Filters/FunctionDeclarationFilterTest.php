<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Token;

class FunctionDeclarationFilterTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var FilterInterface
   */
  private $filter;

  public function setUp() {
    $this->filter = new SingleNodeFilter('Pharborist\Functions\FunctionDeclarationNode', ['foo']);
  }

  public function testPass() {
    $this->assertTrue($this->filter->__invoke(FunctionDeclarationNode::create('foo')));
  }

  public function testFail() {
    $this->assertFalse($this->filter->__invoke(FunctionDeclarationNode::create('bang')));
  }

  public function testTypeFail() {
    $this->assertFalse($this->filter->__invoke(Token::_abstract()));
  }

}
