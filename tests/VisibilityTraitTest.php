<?php

namespace Pharborist;

class VisibilityTraitTest extends \PHPUnit_Framework_TestCase {
  public function testSetVisibility() {
    $method = Parser::parseSnippet('class Foo { public function wrassle() {} }')->getBody()->firstChild();

    $method->setVisibility('private');
    $this->assertStringStartsWith('private', $method->getText());

    $method->setVisibility('protected');
    $this->assertStringStartsWith('protected', $method->getText());

    $method->setVisibility('public');
    $this->assertStringStartsWith('public', $method->getText());

    $method->setVisibility(NULL);
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
}
