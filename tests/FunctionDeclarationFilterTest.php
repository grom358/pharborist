<?php

namespace Pharborist;

use Pharborist\Filters\FunctionDeclarationFilter;

class FunctionDeclarationFilterTest extends \PHPUnit_Framework_TestCase {
  public function testPassFunction() {
    $filter = new FunctionDeclarationFilter();
    $func = Parser::parseSnippet('function foobaz() {}');
    $this->assertTrue($filter($func));
  }

  public function testFailNonFunction() {
    $filter = new FunctionDeclarationFilter();
    $var = Token::variable('$baz');
    $this->assertFalse($filter($var));
  }
}
