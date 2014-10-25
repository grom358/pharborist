<?php

namespace Pharborist;

class UseDeclarationNodeTest extends \PHPUnit_Framework_TestCase {
  public function testAlias() {
    /** @var \Pharborist\Namespaces\UseDeclarationNode $has_alias */
    $has_alias = Parser::parseSnippet('use Cleese as Chapman;')->getDeclarationStatements()[0]->getDeclarations()[0];
    $this->assertInstanceOf('\Pharborist\Namespaces\UseDeclarationNode', $has_alias);
    $this->assertEquals('Cleese as Chapman', $has_alias->getText());
    $this->assertTrue($has_alias->hasAlias());
    /** @var \Pharborist\TokenNode $alias */
    $alias = $has_alias->getAlias();
    $this->assertInstanceOf('\Pharborist\TokenNode', $alias);
    $this->assertEquals(T_STRING, $alias->getType());
    $this->assertEquals('Chapman', $alias->getText());

    /** @var \Pharborist\Namespaces\UseDeclarationNode $no_alias $no_alias */
    $no_alias = Parser::parseSnippet('use Foobaz;')->getDeclarationStatements()[0]->getDeclarations()[0];
    $this->assertInstanceOf('\Pharborist\Namespaces\UseDeclarationNode', $no_alias);
    $this->assertFalse($no_alias->hasAlias());
    $this->assertNull($no_alias->getAlias());

    $alias = new TokenNode(T_STRING, 'Foobar');
    $no_alias->setAlias($alias);
    $this->assertTrue($no_alias->hasAlias());
    $this->assertSame($no_alias->getAlias(), $alias);
    $this->assertEquals('Foobaz as Foobar', $no_alias->getText());

    $this->assertTrue($no_alias->setAlias('Blorf')->hasAlias());
    $alias = $no_alias->getAlias();
    $this->assertInstanceOf('\Pharborist\TokenNode', $alias);
    $this->assertEquals(T_STRING, $alias->getType());
    $this->assertEquals('Foobaz as Blorf', $no_alias->getText());

    $no_alias->setAlias(NULL);
    $this->assertFalse($no_alias->hasAlias());
    $this->assertEquals('Foobaz', $no_alias->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testSetAliasInvalidArgument() {
    /** @var \Pharborist\Namespaces\UseDeclarationNode $has_alias */
    $has_alias = Parser::parseSnippet('use Cleese as Chapman;')->getDeclarationStatements()[0]->getDeclarations()[0];
    $has_alias->setAlias(3.141);
  }
}
