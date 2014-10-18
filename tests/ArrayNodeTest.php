<?php
namespace Pharborist;

class ArrayNodeTest extends \PHPUnit_Framework_TestCase {
  private function toScalar(Node $node) {
    return $node->toValue();
  }

  public function testGetNonIndexedArrayKeys() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("a", "b", "c")');
    $keys = $array->getKeys();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertTrue($keys->isEmpty());
  }

  public function testGetIndexedArrayKeys() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("a" => "apple", "b" => "bear", "c" => "cauldron")');
    $keys = $array->getKeys();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertCount(3, $keys);
    $this->assertSame(['a','b','c'], array_map([$this, 'toScalar'], $keys->toArray()));
  }

  public function testGetKeysRecursive() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array(0 => "foo", 1 => "baz", 2 => array(0 => "a", 1 => "b", 2 => "c"))');
    $keys = $array->getKeys();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertCount(6, $keys);

    $keys = $array->getKeys(FALSE);
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertCount(3, $keys);
    $this->assertSame([0, 1, 2], array_map([$this, 'toScalar'], $keys->toArray()));
  }

  public function testGetValues() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array(0 => "foo", 1 => "baz", 2 => array(0 => "a", 1 => "b", 2 => "c"))');
    $values = $array->getValues();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $values);
    $this->assertCount(5, $values);
    $this->assertSame(['foo','baz','a','b','c'], array_map([$this, 'toScalar'], $values->toArray()));

    $values = $array->getValues(FALSE);
    $this->assertInstanceOf('\Pharborist\NodeCollection', $values);
    $this->assertCount(3, $values);
    $this->assertEquals('foo', $values[0]->toValue());
    $this->assertEquals('baz', $values[1]->toValue());
    $this->assertInstanceOf('\Pharborist\ArrayNode', $values[2]);
    $this->assertSame(['a','b','c'], array_map([$this, 'toScalar'], $values[2]->getValues()->toArray()));
  }
}
