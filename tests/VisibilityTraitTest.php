<?php

namespace Pharborist;

use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\ClassNode;

class VisibilityTraitTest extends \PHPUnit_Framework_TestCase {
  public function testSetVisibility() {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { public function wrassle() {} }');
    /** @var ClassMethodNode $method */
    $method = $class_node->getStatements()[0];

    $method->setVisibility('private');
    $this->assertStringStartsWith('private', $method->getText());

    $method->setVisibility('protected');
    $this->assertStringStartsWith('protected', $method->getText());

    $method->setVisibility('public');
    $this->assertStringStartsWith('public', $method->getText());

    $method->setVisibility(NULL);
    $this->assertNull($method->getVisibility());
    $this->assertStringStartsWith('function', $method->getText());

    $method->setVisibility(T_PRIVATE);
    $this->assertStringStartsWith('private', $method->getText());

    $method->setVisibility(T_PROTECTED);
    $this->assertStringStartsWith('protected', $method->getText());

    $method->setVisibility(T_PUBLIC);
    $this->assertStringStartsWith('public', $method->getText());

    $method->setVisibility(Token::_private());
    $this->assertStringStartsWith('private', $method->getText());

    $method->setVisibility(Token::_protected());
    $this->assertStringStartsWith('protected', $method->getText());

    $method->setVisibility(Token::_public());
    $this->assertStringStartsWith('public', $method->getText());
  }

  public function testSetVisibilityTokenType() {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { public function wrassle() {} }');
    /** @var ClassMethodNode $method */
    $method = $class_node->getStatements()[0];

    $method->setVisibility('private');
    $this->assertSame(T_PRIVATE, $method->getVisibility()->getType());

    $method->setVisibility('protected');
    $this->assertSame(T_PROTECTED, $method->getVisibility()->getType());

    $method->setVisibility('public');
    $this->assertSame(T_PUBLIC, $method->getVisibility()->getType());

    $method->setVisibility(T_PRIVATE);
    $this->assertSame(T_PRIVATE, $method->getVisibility()->getType());

    $method->setVisibility(T_PROTECTED);
    $this->assertSame(T_PROTECTED, $method->getVisibility()->getType());

    $method->setVisibility(T_PUBLIC);
    $this->assertSame(T_PUBLIC, $method->getVisibility()->getType());

    $method->setVisibility(Token::_private());
    $this->assertSame(T_PRIVATE, $method->getVisibility()->getType());

    $method->setVisibility(Token::_protected());
    $this->assertSame(T_PROTECTED, $method->getVisibility()->getType());

    $method->setVisibility(Token::_public());
    $this->assertSame(T_PUBLIC, $method->getVisibility()->getType());
  }

  public function testSetVisibilityTokenText() {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { public function wrassle() {} }');
    /** @var ClassMethodNode $method */
    $method = $class_node->getStatements()[0];

    $method->setVisibility('private');
    $this->assertSame('private', $method->getVisibility()->getText());

    $method->setVisibility('protected');
    $this->assertSame('protected', $method->getVisibility()->getText());

    $method->setVisibility('public');
    $this->assertSame('public', $method->getVisibility()->getText());

    $method->setVisibility(T_PRIVATE);
    $this->assertSame('private', $method->getVisibility()->getText());

    $method->setVisibility(T_PROTECTED);
    $this->assertSame('protected', $method->getVisibility()->getText());

    $method->setVisibility(T_PUBLIC);
    $this->assertSame('public', $method->getVisibility()->getText());

    $method->setVisibility(Token::_private());
    $this->assertSame('private', $method->getVisibility()->getText());

    $method->setVisibility(Token::_protected());
    $this->assertSame('protected', $method->getVisibility()->getText());

    $method->setVisibility(Token::_public());
    $this->assertSame('public', $method->getVisibility()->getText());
  }
}
