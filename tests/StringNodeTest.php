<?php

namespace Pharborist;

use Pharborist\Types\StringNode;

class StringNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetValue() {
    $string = StringNode::create('\'Goodbye, cruel world!\'');
    $this->assertEquals('Goodbye, cruel world!', $string->toValue());

    $string = StringNode::create('"I\'ll harrr to that!"');
    $this->assertEquals("I'll harrr to that!", $string->toValue());

    // Test escaped characters in double quoted string.
    $string = <<<'EOF'
"h\145llo\\n\nw\x6Frld"
EOF;
    $this->assertEquals("hello\\n\nworld", StringNode::create($string)->toValue());

    // Test escaped characters in single quoted string.
    $string = <<<'EOF'
'it\'s \a\\\'live'
EOF;
    $this->assertEquals("it's \\a\\'live", StringNode::create($string)->toValue());
  }
}
