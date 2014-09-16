<?php

namespace Pharborist;

class StringNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetValue() {
    $string = StringNode::create('\'Goodbye, cruel world!\'');
    $this->assertEquals('Goodbye, cruel world!', $string->getValue());

    $string = StringNode::create('"I\'ll harrr to that!"');
    $this->assertEquals("I'll harrr to that!", $string->getValue());

    // Test escaped characters in double quoted string.
    $string = <<<'EOF'
"h\145llo\\n\nw\x6Frld"
EOF;
    $this->assertEquals("hello\\n\nworld", StringNode::create($string)->getValue());

    // Test escaped characters in single quoted string.
    $string = <<<'EOF'
'it\'s \a\\\'live'
EOF;
    $this->assertEquals("it's \\a\\'live", StringNode::create($string)->getValue());
  }
}
