<?php

namespace Pharborist\Filters;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Token;

class FunctionDeclarationFilterTest extends \PHPUnit_Framework_TestCase {

  public function testPass() {
    $this->assertTrue((new FunctionDeclarationFilter(['foo']))->__invoke(FunctionDeclarationNode::create('foo')));
  }

  public function testFail() {
    $this->assertFalse((new FunctionDeclarationFilter(['foo']))->__invoke(FunctionDeclarationNode::create('bang')));
  }

  public function testTypeFail() {
    $this->assertFalse((new FunctionDeclarationFilter(['foo']))->__invoke(Token::_abstract()));
  }

}
