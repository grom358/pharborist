<?php
namespace Pharborist;

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
}
