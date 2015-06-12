<?php

namespace Pharborist;

use Pharborist\Functions\FunctionDeclarationNode;

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

  public function testReturnType() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSnippet('function hello() {}');
    $this->assertFalse($func->hasReturnTypes());
    $this->assertEquals(['void'], $func->getReturnTypes());

    $func = Parser::parseSnippet('/** @return */ function hello() {}');
    $this->assertFalse($func->hasReturnTypes());
    $this->assertEquals(['void'], $func->getReturnTypes());

    $func = Parser::parseSnippet('/** @return void */ function hello() {}');
    $this->assertTrue($func->hasReturnTypes());
    $this->assertEquals(['void'], $func->getReturnTypes());

    $func = Parser::parseSnippet('/** @return string */ function hello() {}');
    $this->assertTrue($func->hasReturnTypes());
    $this->assertEquals(['string'], $func->getReturnTypes());

    $func = Parser::parseSnippet('/** @return string|int */ function hello() {}');
    $this->assertTrue($func->hasReturnTypes());
    $this->assertEquals(['string', 'int'], $func->getReturnTypes());
  }

  public function testMatchReflector() {
    $function = FunctionDeclarationNode::create('array_walk');
    $reflector = new \ReflectionFunction('array_walk');
    // $function->getReference() should return a TokenNode or NULL, which will
    // loosely evaluate to TRUE or FALSE
    $this->assertEquals($function->getReference(), $reflector->returnsReference());
  }
}
