<?php

namespace Pharborist\Filters;

use Pharborist\Parser;
use Pharborist\Token;

class FunctionCallFilterTest extends \PHPUnit_Framework_TestCase {

  public function testPass() {
    $node = Parser::parseSnippet('variable_get();')->children()->get(0);
    $this->assertTrue((new FunctionCallFilter(['variable_get']))->__invoke($node));
  }

  public function testFail() {
    $node = Parser::parseSnippet('variable_set();')->children()->get(0);
    $this->assertFalse((new FunctionCallFilter(['variable_get']))->__invoke($node));
  }

  public function testTypeFail() {
    $this->assertFalse((new FunctionCallFilter(['variable_get']))->__invoke(Token::_abstract()));
  }

}
