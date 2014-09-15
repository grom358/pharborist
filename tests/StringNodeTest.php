<?php

namespace Pharborist;

class StringNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetValue() {
    $string = StringNode::create('\'Goodbye, cruel world!\'');
    $this->assertEquals('Goodbye, cruel world!', $string->getValue());

    $string = StringNode::create('"I\'ll harrr to that!"');
    $this->assertEquals("I'll harrr to that!", $string->getValue());

    // Test escaped characters in double quoted string.
    $this->assertEquals("hello\nworld", StringNode::create('"hello\nworld"')->getValue());

    // Test escaped characters in single quoted string.
    $this->assertEquals('it\'s alive', StringNode::create("'it\\'s alive'")->getValue());
  }
}
