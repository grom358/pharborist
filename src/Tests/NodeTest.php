<?php
namespace Pharborist\Tests;

/**
 * Tests Phaborist\Node.
 * @package Pharborist
 */
use Pharborist\Node;
use Pharborist\Parser;
use Pharborist\SourcePosition;

/**
 * Tests Pharborist\Node
 */
class NodeTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests Pharborist\Node::filter()
   *
   * @covers Pharborist\Node::filter()
   */
  public function testFilter() {
    // Test with a real file.
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $this->assertSame(count($tree->filter('Pharborist\FunctionDeclaration')), 1);
  }

  /**
   * Tests Pharborist\Node::prependChild()
   *
   * @covers Pharborist\Node::prependChild()
   */
  public function testPrependChild() {
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $original_count = count($tree->children);
    $node = new Node();
    $tree->prependChild($node);
    $this->assertEquals($original_count + 1, count($tree->children));
    $this->assertSame($node, reset($tree->children));
  }

  /**
   * Tests Pharborist\Node::appendChild()
   *
   * @covers Pharborist\Node::appendChild()
   */
  public function testAppendChild() {
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $original_count = count($tree->children);
    $node = new Node();
    $tree->appendChild($node);
    $this->assertEquals($original_count + 1, count($tree->children));
    $this->assertSame($node, end($tree->children));
  }

  /**
   * Tests Pharborist\Node::appendChildren()
   *
   * @covers Pharborist\Node::appendChildren()
   */
  public function testAppendChildren() {
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $original_count = count($tree->children);
    $nodes[] = new Node();
    $nodes[] = new Node();
    $tree->appendChildren($nodes);
    $this->assertEquals($original_count + 2, count($tree->children));
    $this->assertSame(end($nodes), end($tree->children));
    $this->assertSame(reset($nodes), prev($tree->children));
  }

  /**
   * Tests Pharborist\Node::getSourcePosition()
   *
   * @covers Pharborist\Node::getSourcePosition()
   */
  public function testGetSourcePosition() {
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');

    // Function declaration is at line 13, column 10.
    $function_nodes = $tree->filter('\Pharborist\FunctionDeclaration');
    $function_node = reset($function_nodes);
    $this->assertSame(13, $function_node->getSourcePosition()->lineNo);
    $this->assertSame(10, $function_node->getSourcePosition()->colNo);

    $sourcePosition = new SourcePosition(1, 1);
    $node = $this->getMock('\Pharborist\Node', array('getSourcePosition'));
    $node->expects($this->exactly(2))
      ->method('getSourcePosition')
      ->will($this->returnValue($sourcePosition));

    $child = new Node();
    $node->appendChild($child);
    $this->assertSame(1, $child->getSourcePosition()->lineNo);
    $this->assertSame(1, $child->getSourcePosition()->colNo);
  }

  /**
   * Tests Pharborist\Node::__toString()
   *
   * @covers Pharborist\Node::__toString()
   */
  public function testToString() {
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');

    $function_nodes = $tree->filter('\Pharborist\FunctionDeclaration');
    $function_node = reset($function_nodes);
    $function_parameters = reset($function_node->parameters);
    $this->assertSame('$bar', (string) $function_parameters);
  }

}
