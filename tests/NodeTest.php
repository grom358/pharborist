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

    $false = function() {
      return FALSE;
    };
    $this->assertNull($node->closest($false));
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
    $node->appendTo($parent);
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
}
