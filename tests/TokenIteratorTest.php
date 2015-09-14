<?php
namespace Pharborist;

class TokenIteratorTest extends \PHPUnit_Framework_TestCase {
  public function testSingle() {
    $test = new TokenNode(T_STRING, 'test', 'source', 1, 0, 1, 0);
    $iterator = new TokenIterator([$test]);
    $peek = $iterator->peek(0);
    $this->assertSame($test, $peek);
    $this->assertNull($iterator->peek(1));

    $this->assertFalse($iterator->hasNext());
    $this->assertNull($iterator->next());
    $this->assertEquals(1, $iterator->getLineNumber());
    $this->assertEquals(5, $iterator->getColumnNumber());
  }

  public function testEmpty() {
    $iterator = new TokenIterator([]);
    $this->assertNull($iterator->peek(0));
    $this->assertEquals(1, $iterator->getLineNumber());
    $this->assertEquals(1, $iterator->getColumnNumber());
  }
}
