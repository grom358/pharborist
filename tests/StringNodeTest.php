<?php

namespace Pharborist;

class StringNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetValue() {
    $string = new StringNode(T_CONSTANT_ENCAPSED_STRING, '\'Goodbye, cruel world!\'');
    $this->assertEquals('Goodbye, cruel world!', $string->getValue());

    $string = new StringNode(T_CONSTANT_ENCAPSED_STRING, '"I\'ll harrr to that!"');
    $this->assertEquals("I'll harrr to that!", $string->getValue());
  }
}
