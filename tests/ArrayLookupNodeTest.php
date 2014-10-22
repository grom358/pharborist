<?php
namespace Pharborist;

class ArrayLookupNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $lookup = ArrayLookupNode::create(Token::variable('$form_state'), new StringNode(T_CONSTANT_ENCAPSED_STRING, "'storage'"));
    $this->assertEquals('$form_state[\'storage\']', $lookup->getText());
  }

  public function testGetKeys() {
    $lookup = Parser::parseExpression('$foo["bar"]["baz"]');
    $keys = $lookup->getKeys();
    $this->assertInternalType('array', $keys);
    $this->assertCount(2, $keys);
    $this->assertInstanceOf('\Pharborist\StringNode', $keys[0]);
    $this->assertEquals('bar', $keys[0]->toValue());
    $this->assertInstanceOf('\Pharborist\StringNode', $keys[1]);
    $this->assertEquals('baz', $keys[1]->toValue());
  }

  public function testHasScalarKeys() {
    $this->assertTrue(Parser::parseExpression('$foo["bar"]["baz"][3]')->hasScalarKeys());
    $this->assertFalse(Parser::parseExpression('$foo[$bar]["baz"]')->hasScalarKeys());
  }

  public function testExtractKeys() {
    $this->assertSame(['bar', 'baz', 3], Parser::parseExpression('$foo["bar"]["baz"][3]')->extractKeys());
  }

  /**
   * @expectedException \DomainException
   */
  public function testExtractNonScalarKeys() {
    Parser::parseExpression('$foo[$bar][baz()][30]')->extractKeys();
  }
}
