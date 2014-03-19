<?php
namespace Pharborist;

/**
 * Tests Phaborist\Parser.
 * @package Pharborist
 */
class ParserTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests Pharborist\Parser::parseFile().
   *
   * @covers Pharborist\Parser::parseFile
   * @covers Pharborist\Parser::parseSource
   */
  public function testParseFile() {
    // Test with a real file.
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $this->assertInstanceOf('Pharborist\Node', $tree);
    $this->assertSame(count($tree->filter('Pharborist\FunctionDeclarationNode')), 1);
    // Test with a non-existant file.
    $tree = Parser::parseFile('no-such-file.php');
    $this->assertFalse($tree);
  }

}
