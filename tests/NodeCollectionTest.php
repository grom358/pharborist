<?php
namespace Pharborist;

use Pharborist\Types\IntegerNode;
use Pharborist\Types\StringNode;

class NodeCollectionTest extends \PHPUnit_Framework_TestCase {
  /**
   * Create mock ParentNode.
   * @return ParentNode
   */
  protected function createParentNode() {
    return $this->getMockForAbstractClass('\Pharborist\ParentNode');
  }

  /**
   * Create mock Node.
   * @return Node
   */
  protected function createNode() {
    static $counter = 1;
    $mock = $this->getMockForAbstractClass('\Pharborist\Node');
    $mock->id = $counter++;
    return $mock;
  }

  public function testParent() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $parents = $collection->parent();
    $this->assertCount(2, $parents);
    $this->assertSame($parent_one, $parents[0]);
    $this->assertSame($parent_two, $parents[1]);
    $parents = $parents->parent();
    $this->assertCount(1, $parents);
    $this->assertSame($root, $parents[0]);
    $parents = $parents->parent();
    $this->assertCount(0, $parents);
  }

  public function testParents() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $parents = $collection->parents();
    $this->assertCount(3, $parents);
    $this->assertSame($root, $parents[0]);
    $this->assertSame($parent_one, $parents[1]);
    $this->assertSame($parent_two, $parents[2]);
  }

  public function testParentsUntil() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $parents = $collection->parentsUntil(function(Node $node) {
      return $node instanceof RootNode;
    });
    $this->assertCount(2, $parents);
    $this->assertSame($parent_one, $parents[0]);
    $this->assertSame($parent_two, $parents[1]);
  }

  public function testClosest() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $matches = $collection->closest(function(Node $node) {
      return $node instanceof ParentNode;
    });
    $this->assertCount(2, $matches);
    $this->assertSame($parent_one, $matches[0]);
    $this->assertSame($parent_two, $matches[1]);
  }

  public function testPrevious() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $first = $this->createNode();
    $parent_one->append($first);
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $second = $this->createNode();
    $parent_two->append($second);
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $matches = $collection->previous();
    $this->assertCount(2, $matches);
    $this->assertSame($first, $matches[0]);
    $this->assertSame($second, $matches[1]);
  }

  public function testPreviousAll() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $three = $this->createNode();
    $three->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $third = $this->createNode();
    $third->appendTo($parent_two);
    $collection = new NodeCollection([$three, $third], FALSE);
    $matches = $collection->previousAll();
    $this->assertCount(4, $matches);
    $this->assertSame($one, $matches[0]);
    $this->assertSame($two, $matches[1]);
    $this->assertSame($first, $matches[2]);
    $this->assertSame($second, $matches[3]);
  }

  public function testPreviousUntil() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $three = $this->createNode();
    $three->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $third = $this->createNode();
    $third->appendTo($parent_two);
    $collection = new NodeCollection([$three, $third], FALSE);
    $matches = $collection->previousUntil(function (Node $node) {
      return $node->previous() === NULL;
    });
    $this->assertCount(2, $matches);
    $this->assertSame($two, $matches[0]);
    $this->assertSame($second, $matches[1]);
  }

  public function testNext() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $first = $this->createNode();
    $parent_one->append($first);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $second = $this->createNode();
    $parent_two->append($second);
    $root->append($parent_two);
    $collection = new NodeCollection([$one, $two]);
    $matches = $collection->next();
    $this->assertCount(2, $matches);
    $this->assertSame($first, $matches[0]);
    $this->assertSame($second, $matches[1]);
  }

  public function testNextAll() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $three = $this->createNode();
    $three->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $third = $this->createNode();
    $third->appendTo($parent_two);
    $collection = new NodeCollection([$one, $first], FALSE);
    $matches = $collection->nextAll();
    $this->assertCount(4, $matches);
    $this->assertSame($two, $matches[0]);
    $this->assertSame($three, $matches[1]);
    $this->assertSame($second, $matches[2]);
    $this->assertSame($third, $matches[3]);
  }

  public function testNextUntil() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $three = $this->createNode();
    $three->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $third = $this->createNode();
    $third->appendTo($parent_two);
    $collection = new NodeCollection([$one, $first], FALSE);
    $matches = $collection->nextUntil(function (Node $node) {
      return $node->next() === NULL;
    });
    $this->assertCount(2, $matches);
    $this->assertSame($two, $matches[0]);
    $this->assertSame($second, $matches[1]);
  }

  public function testSiblings() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $three = $this->createNode();
    $three->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $third = $this->createNode();
    $third->appendTo($parent_two);
    $collection = new NodeCollection([$two, $second], FALSE);
    $matches = $collection->siblings();
    $this->assertCount(4, $matches);
    $this->assertSame($one, $matches[0]);
    $this->assertSame($three, $matches[1]);
    $this->assertSame($first, $matches[2]);
    $this->assertSame($third, $matches[3]);
  }

  public function testFind() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$parent_one, $parent_two]);
    $matches = $collection->find(function () {
      return TRUE;
    });
    $this->assertCount(2, $matches);
    $this->assertSame($one, $matches[0]);
    $this->assertSame($two, $matches[1]);
  }

  public function testHas() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $one = $this->createNode();
    $parent_one->append($one);
    $root->append($parent_one);
    $parent_two = $this->createParentNode();
    $two = $this->createNode();
    $parent_two->append($two);
    $root->append($parent_two);
    $collection = new NodeCollection([$parent_one, $parent_two]);
    $matches = $collection->has(function (Node $node) use ($one) {
      return $node === $one;
    });
    $this->assertCount(1, $matches);
    $this->assertSame($parent_one, $matches[0]);
  }

  public function testFilter() {
    $one = $this->createNode();
    $two = $this->createNode();
    $collection = new NodeCollection([$one, $two], FALSE);
    $matches = $collection->filter(function (Node $node) use ($one) {
      return $node === $one;
    });
    $this->assertCount(1, $matches);
    $this->assertSame($one, $matches[0]);
  }

  public function testNot() {
    $one = $this->createNode();
    $two = $this->createNode();
    $collection = new NodeCollection([$one, $two], FALSE);
    $matches = $collection->not(function (Node $node) use ($one) {
      return $node === $one;
    });
    $this->assertCount(1, $matches);
    $this->assertSame($two, $matches[0]);
  }

  public function testInsertBefore() {
    $root = new RootNode();
    $pivot = $this->createNode();
    $root->append($pivot);
    $test = $this->createNode();
    $collection = new NodeCollection([$test], FALSE);
    $collection->insertBefore($pivot);
    $this->assertSame($test, $pivot->previous());
  }

  public function testBefore() {
    $root = new RootNode();
    $pivot = $this->createNode();
    $root->append($pivot);
    $test = $this->createNode();
    $collection = new NodeCollection([$pivot], FALSE);
    $collection->before($test);
    $this->assertSame($test, $pivot->previous());
  }

  public function testInsertAfter() {
    $root = new RootNode();
    $pivot = $this->createNode();
    $root->append($pivot);
    $test = $this->createNode();
    $collection = new NodeCollection([$test], FALSE);
    $collection->insertAfter($pivot);
    $this->assertSame($test, $pivot->next());
  }

  public function testAfter() {
    $root = new RootNode();
    $pivot = $this->createNode();
    $root->append($pivot);
    $test = $this->createNode();
    $collection = new NodeCollection([$pivot], FALSE);
    $collection->after($test);
    $this->assertSame($test, $pivot->next());
  }

  public function testRemove() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $two = $this->createNode();
    $two->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $collection = new NodeCollection([$one, $first], FALSE);
    $collection->remove();
    $this->assertEquals(1, $parent_one->childCount());
    $this->assertEquals(1, $parent_two->childCount());
  }

  public function testReplaceWith() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->name = 'parent_one';
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->name = 'one';
    $one->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->name = 'parent_two';
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->name = 'first';
    $first->appendTo($parent_two);
    $replacement = new TokenNode(T_STRING, 'replacement');
    $collection = new NodeCollection([$one, $first], FALSE);
    $ret = $collection->replaceWith([$replacement]);
    $this->assertSame($one, $ret[0]);
    $this->assertSame($first, $ret[1]);
    $this->assertSame($replacement, $parent_one->firstChild());
    $this->assertNotSame($replacement, $parent_two->firstChild());
    $this->assertEquals('replacement', $parent_two->firstChild()->getText());
  }

  public function testReplaceAll() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $second = $this->createNode();
    $second->appendTo($parent_two);
    $replacement = new TokenNode(T_STRING, 'replacement');
    $collection = new NodeCollection([$replacement], FALSE);
    $ret = $collection->replaceAll([$one, $first]);
    $this->assertSame($replacement, $ret[0]);
    $this->assertSame($replacement, $parent_one->firstChild());
    $this->assertNotSame($replacement, $parent_two->firstChild());
    $this->assertEquals('replacement', $parent_two->firstChild()->getText());

    $collection->replaceAll($second);
    $this->assertSame($replacement, $parent_two->lastChild());
  }

  public function testAdd() {
    $root = new RootNode();
    $first = $this->createNode();
    $first->appendTo($root);
    $second = $this->createNode();
    $second->appendTo($root);
    $third = $this->createNode();
    $third->appendTo($root);
    $collection = new NodeCollection([], FALSE);
    $collection->add($first);
    $collection->add([$second]);
    $collection->add(new NodeCollection([$third], FALSE));
    $this->assertCount(3, $collection);
    $this->assertSame($first, $collection[0]);
    $this->assertSame($second, $collection[1]);
    $this->assertSame($third, $collection[2]);
  }

  public function testAddTo() {
    $c1 = new NodeCollection([ StringNode::fromValue('foo') ]);
    $c2 = new NodeCollection([ IntegerNode::fromValue(30) ]);
    $union = $c1->addTo($c2);
    $this->assertCount(2, $union);
    $this->assertSame($union, $c2);
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidAdd() {
    $collection = new NodeCollection([], FALSE);
    $collection->add(NULL);
  }

  public function testExists() {
    $root = new RootNode();
    $first = $this->createNode();
    $first->appendTo($root);
    $collection = new NodeCollection([$first], FALSE);
    $this->assertTrue(isset($collection[0]));
  }

  /**
   * @expectedException \BadMethodCallException
   */
  public function testSet() {
    $root = new RootNode();
    $first = $this->createNode();
    $first->appendTo($root);
    $collection = new NodeCollection([$first], FALSE);
    $second = $this->createNode();
    $collection[0] = $second;
  }

  /**
   * @expectedException \BadMethodCallException
   */
  public function testUnset() {
    $root = new RootNode();
    $first = $this->createNode();
    $first->appendTo($root);
    $collection = new NodeCollection([$first], FALSE);
    unset($collection[0]);
  }

  public function testSlice() {
    $root = new RootNode();
    $nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = $this->createNode();
      $root->append($node);
      $nodes[] = $node;
    }
    $collection = new NodeCollection($nodes, FALSE);

    $slice = $collection->slice(4);
    $this->assertCount(1, $slice);

    $slice = $collection->slice(2, 4);
    $this->assertCount(2, $slice);

    $slice = $collection->slice(1, -1);
    $this->assertCount(3, $slice);

    $slice = $collection->slice(1, -2);
    $this->assertCount(2, $slice);

    $slice = $collection->slice(-2, -1);
    $this->assertCount(1, $slice);

    $slice = $collection->slice(-3, -1);
    $this->assertCount(2, $slice);
  }

  public function testMap() {
    $root = new RootNode();
    $parent_one = $this->createParentNode();
    $parent_one->appendTo($root);
    $one = $this->createNode();
    $one->appendTo($parent_one);
    $parent_two = $this->createParentNode();
    $parent_two->appendTo($root);
    $first = $this->createNode();
    $first->appendTo($parent_two);
    $collection = new NodeCollection([$parent_one, $parent_two], FALSE);
    $map = $collection->map(function (ParentNode $node) {
      return $node->firstChild();
    });
    $this->assertCount(2, $map);
    $this->assertSame($one, $map[0]);
    $this->assertSame($first, $map[1]);
  }

  public function testReduce() {
    $first = Token::identifier('hello');
    $second = Token::identifier('world');
    $collection = new NodeCollection([$first, $second], FALSE);
    $ret = $collection->reduce(function ($carry, TokenNode $node) {
      return $carry === '' ? $node->getText() : $carry . ' ' . $node->getText();
    }, '');
    $this->assertEquals('hello world', $ret);
  }

  public function testGet() {
    $first = Token::identifier('hello');
    $second = Token::identifier('world');
    $collection = new NodeCollection([$first, $second], FALSE);
    $this->assertEquals($first, $collection->get(0));
    $this->assertEquals($second, $collection->get(1));
  }

  public function testIndex() {
    $first = Token::identifier('hello');
    $second = Token::identifier('world');
    $not_found = Token::identifier('notfound');
    $collection = new NodeCollection([$first, $second], FALSE);
    $this->assertEquals(0, $collection->indexOf(Filter::is($first)));
    $this->assertEquals(1, $collection->indexOf(Filter::is($second)));
    $this->assertEquals(-1, $collection->indexOf(Filter::is($not_found)));
  }

  public function testSort() {
    $root = new RootNode();
    $parent = $this->createParentNode();
    $root->append($parent);
    $children = [];
    for ($i = 0; $i <= 21; $i++) {
      $children[] = $this->createNode();
    }
    $parent->append($children);
    $collection = new NodeCollection($children, TRUE);
    foreach ($collection as $k => $child) {
      $this->assertSame($children[$k], $child);
    }
  }
}
