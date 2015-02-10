<?php
namespace Pharborist;

class TokenIteratorTest extends \PHPUnit_Framework_TestCase {
  public function testSingle() {
    $test = new TokenNode(T_STRING, 'test', new SourcePosition(NULL, 1, 1));
    $iterator = new TokenIterator([$test]);
    $peek = $iterator->peek(0);
    $this->assertSame($test, $peek);
    $this->assertNull($iterator->peek(1));

    $this->assertNull($iterator->next());
    $source_position = $iterator->getSourcePosition();
    $this->assertEquals(1, $source_position->getLineNumber());
    $this->assertEquals(5, $source_position->getColumnNumber());
  }

  public function testEmpty() {
    $iterator = new TokenIterator([]);
    $this->assertNull($iterator->peek(0));
    $source_position = $iterator->getSourcePosition();
    $this->assertEquals(1, $source_position->getLineNumber());
    $this->assertEquals(1, $source_position->getColumnNumber());
  }
}
