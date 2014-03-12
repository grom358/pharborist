<?php
namespace Pharborist\Tests;

/**
 * Tests Phaborist\Node.
 * @package Pharborist
 */
use Pharborist\Parser;

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
    $this->assertInstanceOf('Pharborist\Node', $tree);
    $this->assertSame(count($tree->filter('Pharborist\FunctionDeclaration')), 1);
  }

}
