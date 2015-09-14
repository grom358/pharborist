<?php
namespace Pharborist;

class ParentNodeTest extends \PHPUnit_Framework_TestCase {
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

  public function testPrepend() {
    $parent = $this->createParentNode();
    $parent->prepend($this->createNode('last'));
    $parent->prepend([$this->createNode('3'), $this->createNode('4')]);
    $this->assertEquals('3', $parent->firstChild()->getText());
    $this->assertEquals('4', $parent->firstChild()->next()->getText());
    $this->assertEquals('last', $parent->firstChild()->next()->next()->getText());
    $parent->prepend(new NodeCollection([$this->createNode('1'), $this->createNode('2')], FALSE));
    $this->assertEquals('1', $parent->firstChild()->getText());
    $this->assertEquals('2', $parent->firstChild()->next()->getText());
    $this->assertEquals('3', $parent->firstChild()->next()->next()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidPrepend() {
    $parent = $this->createParentNode();
    $parent->prepend(NULL);
  }

  public function testAppend() {
    $parent = $this->createParentNode();
    $parent->prepend($this->createNode('first'));
    $parent->append([$this->createNode('1'), $this->createNode('2')]);
    $this->assertEquals('first', $parent->firstChild()->getText());
    $this->assertEquals('1', $parent->firstChild()->next()->getText());
    $this->assertEquals('2', $parent->firstChild()->next()->next()->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalidAppend() {
    $parent = $this->createParentNode();
    $parent->append(NULL);
  }

  public function testHas() {
    $parent = $this->createParentNode();
    $parent->append($this->createNode('one'));
    $parent->append($this->createNode('two'));
    $false = function($node) {
      return FALSE;
    };
    $this->assertFalse($parent->has($false));

    $is_two = function(Node $node) {
      return $node->getText() === 'two';
    };
    $this->assertTrue($parent->has($is_two));

    $sub = $this->createParentNode();
    $sub->append($this->createNode('findMe'));
    $parent->append($sub);
    $is_me = function(Node $node) {
      if ($node instanceof ParentNode) {
        return FALSE;
      }
      return $node->getText() === 'findMe';
    };
    $this->assertTrue($parent->has($is_me));
  }

  public function testIsDescendant() {
    $parent = $this->createParentNode();
    $parent->append($this->createNode('one'));
    $descendant = $this->createNode('two');
    $parent->append($descendant);
    $this->assertTrue($parent->isDescendant($descendant));

    $sub = $this->createParentNode();
    $descendant = $this->createNode('findMe');
    $sub->append($descendant);
    $parent->append($sub);
    $this->assertTrue($parent->isDescendant($descendant));

    $orphan = $this->createNode('orphan');
    $this->assertFalse($parent->isDescendant($orphan));
  }

  public function testWalk() {
    $parent = $this->createParentNode();
    $one = $this->createNode('one');
    $parent->append($one);
    $two = $this->createNode('two');
    $parent->append($two);
    $sub = $this->createParentNode();
    $leaf = $this->createNode('findMe');
    $sub->append($leaf);
    $parent->append($sub);

    $order = [$parent, $one, $two, $sub, $leaf];
    $i = 0;
    $test = function($node) use ($order, &$i) {
      $this->assertSame($order[$i], $node);
      $i++;
    };
    $parent->walk($test);
  }

  public function testWalkAbort() {
    $parent = $this->createParentNode();
    $one = $this->createNode('one');
    $parent->append($one);
    $two = $this->createNode('two');
    $parent->append($two);
    $sub = $this->createParentNode();
    $leaf = $this->createNode('findMe');
    $sub->append($leaf);
    $parent->append($sub);

    $visited = [];
    $test = function($node) use (&$visited) {
      $visited[] = $node;
      if ($node instanceof ParentNode && $node->parent() !== NULL) {
        return FALSE;
      }
      return TRUE;
    };
    $parent->walk($test);
    $this->assertEquals([$parent, $one, $two, $sub], $visited);
  }

  public function testSourcePosition() {
    $token = new TokenNode(T_STRING, 'test', 'source', 4, 0, 2);
    $grandparent = $this->createParentNode();
    $grandparent->append($token);
    $parent = $this->createParentNode();
    $grandparent->append($parent);
    $this->assertEquals('source', $parent->getFilename());
    $this->assertEquals(4, $parent->getLineNumber());
    $this->assertEquals(2, $parent->getColumnNumber());
  }

  public function testLastToken() {
    $token = new TokenNode(T_STRING, 'test');
    $grandparent = $this->createParentNode();
    $parent = $this->createParentNode();
    $grandparent->append($parent);
    $parent->append($token);
    $this->assertSame($token, $grandparent->lastToken());
  }

  public function testClone() {
    $parent = $this->createParentNode();
    $one = $this->createNode('one');
    $two = $this->createNode('two');
    $three = $this->createNode('three');
    $parent->append([$one, $two, $three]);
    $copy = clone $parent;
    // Test our copy equals original.
    $this->assertEquals('one', $copy->firstChild()->getText());
    $this->assertSame($copy, $copy->firstChild()->parent());
    $this->assertEquals('two', $copy->firstChild()->next()->getText());
    $this->assertEquals($copy, $copy->firstChild()->next()->parent());
    $this->assertEquals('three', $copy->firstChild()->next()->next()->getText());
    $this->assertEquals($copy, $copy->firstChild()->next()->next()->parent());
    $this->assertNull($copy->firstChild()->next()->next()->next());
    $this->assertEquals('three', $copy->lastChild()->getText());
    $this->assertEquals($copy, $copy->lastChild()->parent());
    $this->assertEquals('two', $copy->lastChild()->previous()->getText());
    $this->assertEquals($copy, $copy->lastChild()->previous()->parent());
    $this->assertEquals('one', $copy->lastChild()->previous()->previous()->getText());
    $this->assertEquals($copy, $copy->lastChild()->previous()->previous()->parent());
    $this->assertNull($copy->lastChild()->previous()->previous()->previous());
    // Test clone was a deep copy.
    $this->assertNotSame($parent, $copy);
    $this->assertNotSame($one, $copy->firstChild());
    $this->assertNotSame($two, $copy->firstChild()->next());
    $this->assertNotSame($three, $copy->lastChild());
  }
}
