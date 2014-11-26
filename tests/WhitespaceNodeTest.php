<?php
namespace Pharborist;

class WhitespaceNodeTest extends \PHPUnit_Framework_TestCase {
  public function testNewlineCount() {
    $node = Token::whitespace(" \n  \n\n \n  ");
    $this->assertEquals(4, $node->getNewlineCount());
  }
}
