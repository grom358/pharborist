<?php

namespace Pharborist;

use Pharborist\Filters\FunctionDeclarationFilter;

class FunctionDeclarationFilterTest extends \PHPUnit_Framework_TestCase {
  public function testPassFunction() {
    $filter = new FunctionDeclarationFilter(['foobaz']);
    $func = Parser::parseSnippet('function foobaz() {}');
    $this->assertTrue($filter($func));
  }

  public function testFailIncorrectFunction() {
    $filter = new FunctionDeclarationFilter(['foobaz']);
    $func = Parser::parseSnippet('function wambooli() {}');
    $this->assertFalse($filter($func));
  }

  public function testFailNonFunction() {
    $filter = new FunctionDeclarationFilter(['foobaz']);
    $var = Token::variable('$baz');
    $this->assertFalse($filter($var));
  }
}
