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
    $parent->prepend(new NodeCollection([$this->createNode('1'), $this->createNode('2')]));
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

  public function testSourcePosition() {
    $position = new SourcePosition(4, 2);
    $token = new TokenNode(T_STRING, 'test', $position);
    $grandparent = $this->createParentNode();
    $grandparent->append($token);
    $parent = $this->createParentNode();
    $grandparent->append($parent);
    $this->assertEquals(4, $parent->getSourcePosition()->getLineNumber());
    $this->assertEquals(2, $parent->getSourcePosition()->getColumnNumber());
  }

  public function testLastToken() {
    $position = new SourcePosition(4, 2);
    $token = new TokenNode(T_STRING, 'test', $position);
    $grandparent = $this->createParentNode();
    $parent = $this->createParentNode();
    $grandparent->append($parent);
    $parent->append($token);
    $this->assertSame($token, $grandparent->lastToken());
  }
}
