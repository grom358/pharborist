<?php

namespace Pharborist;

class FunctionDeclarationNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCloneAsMethodOf() {
    /** @var \Pharborist\Objects\ClassNode $class */
    $class = Parser::parseSnippet('class Camelot {}');
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSnippet('function sing_goofy_song() {}');

    $func->cloneAsMethodOf($class);
    $this->assertTrue($class->hasMethod('sing_goofy_song'));
  }

  public function testName() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSnippet('function hello() {}');
    $this->assertEquals('hello', $func->getName()->getText());
    $func->setName('test');
    $this->assertEquals('test', $func->getName()->getText());
  }

  public function testReference() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSnippet('function hello() {}');
    $this->assertNull($func->getReference());
    $func->setReference(TRUE);
    $this->assertNotNull($func->getReference());
    $this->assertEquals('&', $func->getReference()->getText());
    $this->assertEquals('function &hello() {}', $func->getText());
    $func->setReference(FALSE);
    $this->assertNull($func->getReference());
    $this->assertEquals('function hello() {}', $func->getText());
  }
}
