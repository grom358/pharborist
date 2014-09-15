<?php

namespace Pharborist;

class StringNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetValue() {
    $string = StringNode::create('\'Goodbye, cruel world!\'');
    $this->assertEquals('Goodbye, cruel world!', $string->getValue());

    $string = StringNode::create('"I\'ll harrr to that!"');
    $this->assertEquals("I'll harrr to that!", $string->getValue());
  }
}
