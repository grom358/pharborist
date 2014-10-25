<?php
namespace Pharborist;

use Pharborist\Types\ArrayNode;

class ArrayNodeTest extends \PHPUnit_Framework_TestCase {
  private function toScalar(Node $node) {
    return $node->toValue();
  }

  public function testHasKey() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("a", "b", "c")');
    $this->assertTrue($array->hasKey(0));

    $array = Parser::parseExpression('array("a" => "apple", "b" => "bear", "c" => "cauldron")');
    $this->assertTrue($array->hasKey('a'));
    $this->assertFalse($array->hasKey('d'));

    $array = Parser::parseExpression('array(0 => "foo", 1 => "baz", 2 => array(0 => "a", 1 => "b", 2 => "c"))');
    $this->assertTrue($array->hasKey(1));
    $this->assertFalse($array->hasKey('2'));

    $array = Parser::parseExpression('array(0 => "foo", 1 => array(0 => "a", 1 => "b", 2 => "c"))');
    $this->assertTrue($array->hasKey(1));
    $this->assertTrue($array->hasKey(2));
    $this->assertFalse($array->hasKey(2, FALSE));

    $array = Parser::parseExpression('array($key => "hurrr")');
    $this->assertFalse($array->hasKey('$key'));
    $var = Token::variable('$key');
    $this->assertTrue($array->hasKey($var));
  }

  public function testGetNonIndexedArrayKeys() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("a", "b", "c")');
    $keys = $array->getKeys();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertCount(3, $keys);
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $keys[0]);
    $this->assertSame(0, $keys[0]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $keys[1]);
    $this->assertSame(1, $keys[1]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $keys[2]);
    $this->assertSame(2, $keys[2]->toValue());
  }

  public function testGetKeysMixed() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("a", "k" => "v", "b")');
    $keys = $array->getKeys();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $keys);
    $this->assertCount(3, $keys);
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $keys[0]);
    $this->assertSame(0, $keys[0]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $keys[1]);
    $this->assertSame('k', $keys[1]->toValue());
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $keys[2]);
    $this->assertSame(1, $keys[2]->toValue());
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
    $this->assertInstanceOf('\Pharborist\Types\ArrayNode', $values[2]);
    $this->assertSame(['a','b','c'], array_map([$this, 'toScalar'], $values[2]->getValues()->toArray()));
  }

  public function testMultidimensional() {
    /** @var ArrayNode $array */
    $array = Parser::parseExpression('array("foo", "k" => "baz", 2 => array("nested"))');
    $this->assertTrue($array->isMultidimensional());
  }
}
