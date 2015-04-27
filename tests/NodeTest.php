<?php
namespace Pharborist;

use Pharborist\Types\ArrayNode;
use Pharborist\Types\ArrayPairNode;

class NodeTest extends \PHPUnit_Framework_TestCase {
  /**
   * Create mock ParentNode.
   * @return ParentNode
   */
  protected function createParentNode() {
    return $this->getMockForAbstractClass('\Pharborist\ParentNode');
  }

  /**
   * Create mock Node.
   * @param $text
   * @return Node
   */
  protected function createNode($text) {
    $mock = $this->getMockForAbstractClass('\Pharborist\Node');
    $mock->expects($this->any())
      ->method('getText')
      ->will($this->returnValue($text));
    return $mock;
  }

  public function testParents() {
    $grandparent = $this->createParentNode();
    $parent = $this->createParentNode();
    $parent->appendTo($grandparent);
    $node = $this->createNode('child');
    $node->appendTo($parent);

    $false = function() {
      return FALSE;
    };
    $true = function() {
      return TRUE;
    };

    $this->assertNotNull($node->parent());
    $this->assertSame($parent, $node->parent());
    $this->assertNull($node->parent($false));

    $parents = $node->parents();
    $this->assertCount(2, $parents);
    $this->assertCount(2, $node->parents($true));

    $until = function($node) use($grandparent) {
      return $node === $grandparent;
    };

    $parents = $node->parentsUntil($until);
    $this->assertCount(1, $parents);

    $parents = $node->parentsUntil($until, TRUE);
    $this->assertCount(2, $parents);
  }

  public function testClosest() {
    $grandparent = $this->createParentNode();
    $parent = $this->createParentNode();
    $parent->appendTo($grandparent);
    $node = $this->createNode('test');
    $node->appendTo($parent);

    $is_test = function($node) {
      /** @var Node $node */
      return $node->getText() === 'test';
    };
    $this->assertSame($node, $node->closest($is_test));

    $is_grandparent = function($node) use($grandparent) {
      /** @var Node $node */
      return $node === $grandparent;
    };
    $this->assertSame($grandparent, $node->closest($is_grandparent));

    $false = function() {
      return FALSE;
    };
    $this->assertNull($node->closest($false));
  }

  public function testFurthest() {
    $grandparent = $this->createParentNode();
    $parent = $this->createParentNode();
    $parent->appendTo($grandparent);
    $node = $this->createNode('test');
    $parent->append($node);
    $parent->append($this->createNode('me'));

    $is_test = function($node) {
      /** @var Node $node */
      return $node->getText() === 'test';
    };
    $this->assertSame($node, $node->furthest($is_test));

    $is_grandparent = function($node) use ($grandparent) {
      /** @var Node $node */
      return $node === $grandparent;
    };
    $this->assertSame($grandparent, $node->furthest($is_grandparent));

    $false = function() {
      return FALSE;
    };
    $this->assertNull($node->furthest($false));

    $has_parent = function($node) {
      /** @var Node $node */
      return $node->parent() !== NULL;
    };
    $this->assertSame($parent, $node->furthest($has_parent));
  }

  public function testIndex() {
    $parent = $this->createParentNode();
    /** @var Node[] $nodes */
    $nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = $this->createNode($i);
      $nodes[] = $node;
      $node->appendTo($parent);
    }

    $third = $nodes[2];
    $this->assertEquals(2, $third->index());
  }

  public function testSiblings() {
    $parent = $this->createParentNode();
    /** @var Node[] $nodes */
    $nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = $this->createNode($i);
      $nodes[] = $node;
      $node->appendTo($parent);
    }

    $this->assertNull($nodes[0]->previous());
    $this->assertNull($nodes[4]->next());

    $this->assertCount(2, $nodes[2]->previousAll());
    $this->assertCount(2, $nodes[2]->nextAll());

    $false = function() { return FALSE; };

    $this->assertNull($nodes[2]->previous($false));
    $this->assertNull($nodes[2]->next($false));

    $until = function($node) use($nodes) {
      return $node === $nodes[2];
    };

    $this->assertCount(1, $nodes[4]->previousUntil($until));
    $this->assertCount(1, $nodes[0]->nextUntil($until));

    $this->assertCount(2, $nodes[4]->previousUntil($until, TRUE));
    $this->assertCount(2, $nodes[0]->nextUntil($until, TRUE));
  }

  public function testInsertBefore() {
    $parent = $this->createParentNode();
    $node = $this->createNode('pivot');
    $node->appendTo($parent);
    $before_node = $this->createNode('singleNode');
    $before_node->insertBefore($node);
    $this->assertSame($node->previous(), $before_node);
    $this->assertEquals('singleNode', $node->previous()->getText());

    /** @var Node[] $targets */
    $targets = [$node, $before_node];
    $before_node = $this->createNode('beforeNode');
    $before_node->insertBefore($targets);
    $this->assertSame($targets[0]->previous(), $before_node);
    $this->assertEquals('beforeNode', $targets[1]->previous()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidInsertBefore() {
    $node = $this->createNode('test');
    $node->insertBefore(NULL);
  }

  public function testBefore() {
    $parent = $this->createParentNode();
    $node = $this->createNode('pivot');
    $node->appendTo($parent);
    $before_node = $this->createNode('beforeNode');
    $node->before($before_node);
    $this->assertSame($node->previous(), $before_node);
    $this->assertEquals('beforeNode', $node->previous()->getText());

    /** @var Node[] $nodes */
    $nodes = [$this->createNode('first'), $this->createNode('second')];
    $before_node->before($nodes);
    $this->assertEquals('second', $before_node->previous()->getText());
    $this->assertEquals('first', $before_node->previous()->previous()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidBefore() {
    $node = $this->createNode('test');
    $node->before(NULL);
  }

  public function testInsertAfter() {
    $parent = $this->createParentNode();
    $node = $this->createNode('pivot');
    $node->appendTo($parent);
    $after_node = $this->createNode('singleNode');
    $after_node->insertAfter($node);
    $this->assertSame($node->next(), $after_node);
    $this->assertEquals('singleNode', $node->next()->getText());

    /** @var Node[] $targets */
    $targets = [$node, $after_node];
    $after_node = $this->createNode('afterNode');
    $after_node->insertAfter($targets);
    $this->assertSame($targets[0]->next(), $after_node);
    $this->assertEquals('afterNode', $targets[1]->next()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidInsertAfter() {
    $node = $this->createNode('test');
    $node->insertAfter(NULL);
  }

  public function testAfter() {
    $parent = $this->createParentNode();
    $node = $this->createNode('pivot');
    $node->appendTo($parent);
    $after_node = $this->createNode('afterNode');
    $node->after($after_node);
    $this->assertSame($node->next(), $after_node);
    $this->assertEquals('afterNode', $node->next()->getText());

    /** @var Node[] $nodes */
    $nodes = [$this->createNode('first'), $this->createNode('second')];
    $after_node->after($nodes);
    $this->assertEquals('first', $after_node->next()->getText());
    $this->assertEquals('second', $after_node->next()->next()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidAfter() {
    $node = $this->createNode('test');
    $node->after(NULL);
  }

  public function testPrependTo() {
    $parent = $this->createParentNode();
    $node = $this->createNode('second');
    $node->prependTo($parent);
    $this->assertSame($parent, $node->parent());
    $this->assertSame($node, $node->parent()->firstChild());
    $node = $this->createNode('first');
    $node->prependTo($parent);
    $this->assertSame($parent, $node->parent());
    $this->assertSame($node, $node->parent()->firstChild());
    $this->assertEquals('second', $node->next()->getText());

    /** @var ParentNode[] $targets */
    $targets = [$this->createParentNode(), $this->createParentNode()];
    $node = $this->createNode('head');
    $node->prependTo($targets);
    $this->assertSame($node, $targets[0]->firstChild());
    $this->assertNotSame($node, $targets[1]->firstChild());
    $this->assertEquals('head', $targets[1]->firstChild()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidPrependTo() {
    $node = $this->createNode('test');
    $node->prependTo(NULL);
  }

  public function testAppendTo() {
    $parent = $this->createParentNode();
    $node = $this->createNode('first');
    $node->appendTo($parent);
    $this->assertSame($parent, $node->parent());
    $this->assertSame($node, $node->parent()->firstChild());
    $node = $this->createNode('second');
    $node->appendTo($parent);
    $this->assertSame($parent, $node->parent());
    $this->assertSame($node, $node->parent()->lastChild());
    $this->assertEquals('first', $node->previous()->getText());

    /** @var ParentNode[] $targets */
    $targets = [$this->createParentNode(), $this->createParentNode()];
    $node = $this->createNode('tail');
    $node->appendTo($targets);
    $this->assertSame($node, $targets[0]->firstChild());
    $this->assertNotSame($node, $targets[1]->firstChild());
    $this->assertEquals('tail', $targets[1]->firstChild()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidAppendTo() {
    $node = $this->createNode('test');
    $node->appendTo(NULL);
  }

  public function testRemove() {
    $node = $this->createNode('test');
    $parent = $this->createParentNode();
    $parent->addChild($node, 'test');
    $this->assertSame($parent, $node->parent());
    $node->remove();
    $this->assertNull($node->parent());

    $parent->append($this->createNode('pivot'));
    $node->prependTo($parent);
    $this->assertEquals('test', $parent->firstChild()->getText());
    $node->remove();
    $this->assertEquals('pivot', $parent->firstChild()->getText());
    $node->appendTo($parent);
    $this->assertEquals('test', $parent->lastChild()->getText());
    $node->remove();
    $this->assertEquals('pivot', $parent->firstChild()->getText());
    $node->appendTo($parent);
    $parent->append($this->createNode('last'));
    $this->assertEquals('pivot', $parent->firstChild()->getText());
    $this->assertSame($node, $parent->firstChild()->next());
    $this->assertEquals('last', $parent->lastChild()->getText());
    $node->remove();
    $this->assertEquals('pivot', $parent->firstChild()->getText());
    $this->assertSame('last', $parent->firstChild()->next()->getText());
  }

  public function testReplaceWith() {
    $original = $this->createNode('original');
    $replacement = $this->createNode('replacement');
    $original->replaceWith($replacement);
    $this->assertEquals('original', $original->getText());

    $parent = $this->createParentNode();
    $original->appendTo($parent);
    $this->assertSame($original, $parent->firstChild());
    $this->assertEquals('original', $parent->firstChild()->getText());
    $original->replaceWith($replacement);
    $this->assertSame($replacement, $parent->firstChild());
    $this->assertEquals('replacement', $parent->firstChild()->getText());

    $replacements = [$this->createNode('first'), $this->createNode('second')];
    $replacement->replaceWith($replacements);
    $this->assertEquals('first', $parent->firstChild()->getText());
    $this->assertEquals('second', $parent->lastChild()->getText());

    $parent = $this->createParentNode();
    $this->createNode('first')->appendTo($parent);
    $second = $this->createNode('second');
    $second->appendTo($parent);
    $this->createNode('third')->appendTo($parent);
    $second->replaceWith($this->createNode('replacement'));
    $this->assertEquals('replacement', $parent->firstChild()->next()->getText());

    $parent = $this->createParentNode();
    $original = $this->createNode('original')->appendTo($parent);
    $original->replaceWith(function(Node $node) use ($original) {
      $this->assertSame($node, $original);
      return $this->createNode('replacement');
    });
    $this->assertEquals('replacement', $parent->firstChild()->getText());
  }

  /**
   * Test replacing a node with itself.
   */
  public function testReplaceWithSelf() {
    $original = $this->createNode('original');
    $original->replaceWith($original);
    $this->assertEquals('original', $original->getText());
  }

  /**
   * Test replacing a node with collection containing itself.
   */
  public function testReplaceWithContainsSelf() {
    $original = $this->createNode('original');
    $parent = $this->createParentNode();
    $original->appendTo($parent);

    // Test replacing with collection only containing node being replaced.
    $original->replaceWith([$original]);
    $this->assertEquals('original', $parent->firstChild()->getText());

    // Test replacing with collection that contains node being replaced.
    $replacements = [
      $this->createNode('replacement_before'),
      $original,
      $this->createNode('replacement_after')
    ];
    $original->replaceWith($replacements);
    $this->assertEquals('replacement_before', $parent->firstChild()->getText());
    $this->assertEquals('original', $parent->firstChild()->next()->getText());
    $this->assertEquals('replacement_after', $parent->lastChild()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidReplaceWith() {
    $node = $this->createNode('test');
    $node->appendTo($this->createParentNode());
    $node->replaceWith(NULL);
  }

  public function testReplaceAll() {
    $original = $this->createNode('original');
    $replacement = $this->createNode('replacement');
    $replacement->replaceAll($original);
    $this->assertEquals('original', $original->getText());

    $parent = $this->createParentNode();
    $original->appendTo($parent);
    $this->assertSame($original, $parent->firstChild());
    $this->assertEquals('original', $parent->firstChild()->getText());
    $replacement->replaceAll($original);
    $this->assertSame($replacement, $parent->firstChild());
    $this->assertEquals('replacement', $parent->firstChild()->getText());

    /** @var ParentNode[] $parents */
    $parents = [$this->createParentNode(), $this->createParentNode()];
    /** @var Node[] $targets */
    $targets = [];
    foreach ($parents as $parent) {
      $node = $this->createNode('original');
      $node->appendTo($parent);
      $targets[] = $node;
    }
    $replacement = $this->createNode('replacement');
    $replacement->replaceAll($targets);
    $this->assertSame($replacement, $parents[0]->firstChild());
    $this->assertNotSame($replacement, $parents[1]->firstChild());
    $this->assertEquals('replacement', $parents[1]->firstChild()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidReplaceAll() {
    $node = $this->createNode('test');
    $node->replaceAll(NULL);
  }

  public function testSwapWith() {
    $parent = $this->createParentNode();
    $first = $this->createNode('first');
    $second = $this->createNode('second');
    $first->appendTo($parent);
    $second->appendTo($parent);
    $first->swapWith($second);
    $this->assertSame($first, $parent->lastChild());
    $this->assertSame($second, $parent->firstChild());

    $first->swapWith($second);
    $this->assertSame($first, $parent->firstChild());
    $this->assertSame($second, $parent->lastChild());

    $another_parent = $this->createParentNode();
    $third = $this->createNode('third');
    $third->appendTo($another_parent);
    $first->swapWith($third);
    $this->assertSame($another_parent, $first->parent());
    $this->assertSame($parent, $third->parent());
    $this->assertSame($third, $second->previous());
    $this->assertSame($second, $third->next());
  }

  public function testFromScalar() {
    $string = Node::fromValue('foobar');
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $string);
    $this->assertEquals("'foobar'", $string->getText());

    $string = Node::fromValue("'foobaz'");
    $this->assertEquals("'\\'foobaz\\''", $string->getText());

    $string = Node::fromValue('Hi, $foobaz');
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $string);
    $this->assertEquals("'Hi, \$foobaz'", $string->getText());

    $string = Node::fromValue('"Yippee ki-yay, $buddeh!"');
    $this->assertEquals("'\"Yippee ki-yay, \$buddeh!\"'", $string->getText());

    $integer = Node::fromValue(30);
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $integer);
    $this->assertEquals('30', $integer->getText());

    $float = Node::fromValue(3.14156);
    $this->assertInstanceOf('\Pharborist\Types\FloatNode', $float);
    $this->assertEquals('3.14156', $float->getText());

    $true = Node::fromValue(TRUE);
    $this->assertInstanceOf('\Pharborist\Types\TrueNode', $true);
    $this->assertTrue($true->toValue());
    $this->assertEquals('TRUE', $true->getText());

    $false = Node::fromValue(FALSE);
    $this->assertInstanceOf('\Pharborist\Types\FalseNode', $false);
    $this->assertFalse($false->toValue());
    $this->assertEquals('FALSE', $false->getText());

    $null = Node::fromValue(NULL);
    $this->assertInstanceOf('\Pharborist\Types\NullNode', $null);
    $this->assertEquals('NULL', $null->getText());

    /** @var ArrayNode $array */
    $array = Node::fromValue(array('hello', 30, 3.14156, TRUE, FALSE, NULL, 'key' => 'value', 42 => 'num'));
    $elements = $array->getElements();
    /** @var ArrayPairNode $pair */
    $pair = $elements[0];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $element);
    $this->assertEquals("'hello'", $element->getText());
    $pair = $elements[1];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $element);
    $this->assertEquals('30', $element->getText());
    $pair = $elements[2];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\FloatNode', $element);
    $this->assertEquals('3.14156', $element->getText());
    $pair = $elements[3];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\TrueNode', $element);
    $this->assertEquals('TRUE', $element->getText());
    $pair = $elements[4];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\FalseNode', $element);
    $this->assertEquals('FALSE', $element->getText());
    $pair = $elements[5];
    $element = $pair->getValue();
    $this->assertInstanceOf('\Pharborist\Types\NullNode', $element);
    $this->assertEquals('NULL', $element->getText());
    $pair = $elements[6];
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $pair->getKey());
    $this->assertEquals("'key'", $pair->getKey()->getText());
    $this->assertEquals("'value'", $pair->getValue()->getText());
    $pair = $elements[7];
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $pair->getKey());
    $this->assertEquals('42', $pair->getKey()->getText());
    $this->assertEquals("'num'", $pair->getValue()->getText());
  }

  public function testToValue() {
    $string = Node::fromValue('foobar');
    $this->assertInstanceOf('\Pharborist\Types\StringNode', $string);
    $this->assertSame('foobar', $string->toValue());

    $integer = Node::fromValue(30);
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $integer);
    $this->assertSame(30, $integer->toValue());

    $float = Node::fromValue(3.14156);
    $this->assertInstanceOf('\Pharborist\Types\FloatNode', $float);
    $this->assertSame(3.14156, $float->toValue());

    $true = Node::fromValue(TRUE);
    $this->assertInstanceOf('\Pharborist\Types\TrueNode', $true);
    $this->assertSame(TRUE, $true->toValue());

    $false = Node::fromValue(FALSE);
    $this->assertInstanceOf('\Pharborist\Types\FalseNode', $false);
    $this->assertSame(FALSE, $false->toValue());

    $null = Node::fromValue(NULL);
    $this->assertInstanceOf('\Pharborist\Types\NullNode', $null);
    $this->assertNull($null->toValue());

    /** @var ArrayNode $array */
    $array = Node::fromValue(array('hello', 30, 3.14156, TRUE, FALSE, NULL, 'key' => 'value', 42 => 'num'));
    $items = $array->toValue();
    $this->assertSame('hello', $items[0]);
    $this->assertSame(30, $items[1]);
    $this->assertSame(3.14156, $items[2]);
    $this->assertTrue($items[3]);
    $this->assertFalse($items[4]);
    $this->assertNull($items[5]);
    $this->assertArrayHasKey('key', $items);
    $this->assertSame('value', $items['key']);
    $this->assertArrayHasKey(42, $items);
    $this->assertSame('num', $items[42]);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testFromScalarInvalidArgument() {
    $node = Node::fromValue(new \stdClass());
  }

  public function testGetStatement() {
    $function = <<<END
function foobaz() {
  return foo();
}
END;
    $function = Parser::parseSnippet($function);
    $foo = $function->find(Filter::isFunctionCall('foo'))->get(0)->getStatement();
    $this->assertInstanceOf('\Pharborist\StatementNode', $foo);
    $this->assertEquals('return foo();', $foo->getText());
    $this->assertSame($function, $function->getStatement());
  }

  public function testRoot() {
    $source = <<<END
<?php
test();
END;
    $tree = Parser::parseSource($source);
    $node = $tree->firstChild()->next();
    $this->assertTrue($node->hasRoot());
    $this->assertSame($tree, $node->getRoot());

    $node = $this->createNode('orphan');
    $this->assertFalse($node->hasRoot());
    $this->assertNull($node->getRoot());
  }
}
