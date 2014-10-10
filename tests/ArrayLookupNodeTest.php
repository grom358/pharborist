<?php
namespace Pharborist;

class ArrayLookupNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $lookup = ArrayLookupNode::create(Token::variable('$form_state'), new StringNode(T_CONSTANT_ENCAPSED_STRING, "'storage'"));
    $this->assertEquals('$form_state[\'storage\']', $lookup->getText());
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
