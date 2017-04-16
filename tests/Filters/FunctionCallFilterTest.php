<?php

namespace Pharborist\Filters;

use Pharborist\Parser;
use Pharborist\Token;

class FunctionCallFilterTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var FilterInterface
   */
  private $filter;

  public function setUp() {
    $this->filter = new SingleNodeFilter('Pharborist\Functions\FunctionCallNode', ['variable_get']);
  }

  public function testPass() {
    $node = Parser::parseSnippet('variable_get();')->children()->get(0);
    $this->assertTrue($this->filter->__invoke($node));
  }

  public function testFail() {
    $node = Parser::parseSnippet('variable_set();')->children()->get(0);
    $this->assertFalse($this->filter->__invoke($node));
  }

  public function testTypeFail() {
    $this->assertFalse($this->filter->__invoke(Token::_abstract()));
  }

}
