<?php
namespace Pharborist;

use Pharborist\Namespaces\UseDeclarationNode;

class UseDeclarationNodeTest extends \PHPUnit_Framework_TestCase {

  public function testImports() {
    $snippet = <<<'EOF'
use Foobar;
use Cleese as Chapman;
use function MyNamespace\test;
use function MyNamespace\test as my_func;
use const MyNamespace\TEST_CONST;
use const MyNamespace\TEST_CONST as MY_CONST;
EOF;
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode $use_declaration_block */
    $use_declaration_block = Parser::parseSnippet($snippet);
    $declarations = $use_declaration_block->getDeclarationStatements();

    $declaration = $declarations[0]->getDeclarations()[0];
    $this->assertTrue($declaration->isClass());
    $this->assertFalse($declaration->isFunction());
    $this->assertFalse($declaration->isConst());
    $this->assertFalse($declaration->hasAlias());
    $this->assertEquals('\Foobar', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('Foobar', $declaration->getName()->getText());
    $this->assertEquals('Foobar', $declaration->getBoundedName());

    $declaration = $declarations[1]->getDeclarations()[0];
    $this->assertTrue($declaration->isClass());
    $this->assertFalse($declaration->isFunction());
    $this->assertFalse($declaration->isConst());
    $this->assertTrue($declaration->hasAlias());
    $this->assertEquals('\Cleese', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('Cleese', $declaration->getName()->getText());
    $this->assertEquals('Chapman', $declaration->getAlias()->getText());
    $this->assertEquals('Chapman', $declaration->getBoundedName());

    $declaration = $declarations[2]->getDeclarations()[0];
    $this->assertFalse($declaration->isClass());
    $this->assertTrue($declaration->isFunction());
    $this->assertFalse($declaration->isConst());
    $this->assertFalse($declaration->hasAlias());
    $this->assertEquals('\MyNamespace\test', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('MyNamespace\test', $declaration->getName()->getText());
    $this->assertEquals('test', $declaration->getBoundedName());

    $declaration = $declarations[3]->getDeclarations()[0];
    $this->assertFalse($declaration->isClass());
    $this->assertTrue($declaration->isFunction());
    $this->assertFalse($declaration->isConst());
    $this->assertTrue($declaration->hasAlias());
    $this->assertEquals('\MyNamespace\test', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('MyNamespace\test', $declaration->getName()->getText());
    $this->assertEquals('my_func', $declaration->getAlias()->getText());
    $this->assertEquals('my_func', $declaration->getBoundedName());

    $declaration = $declarations[4]->getDeclarations()[0];
    $this->assertFalse($declaration->isClass());
    $this->assertFalse($declaration->isFunction());
    $this->assertTrue($declaration->isConst());
    $this->assertFalse($declaration->hasAlias());
    $this->assertEquals('\MyNamespace\TEST_CONST', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('MyNamespace\TEST_CONST', $declaration->getName()->getText());
    $this->assertEquals('TEST_CONST', $declaration->getBoundedName());

    $declaration = $declarations[5]->getDeclarations()[0];
    $this->assertFalse($declaration->isClass());
    $this->assertFalse($declaration->isFunction());
    $this->assertTrue($declaration->isConst());
    $this->assertTrue($declaration->hasAlias());
    $this->assertEquals('\MyNamespace\TEST_CONST', $declaration->getName()->getAbsolutePath());
    $this->assertEquals('MyNamespace\TEST_CONST', $declaration->getName()->getText());
    $this->assertEquals('MY_CONST', $declaration->getAlias()->getText());
    $this->assertEquals('MY_CONST', $declaration->getBoundedName());
  }

  public function testSetAlias() {
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode $declaration_block */
    $declaration_block = Parser::parseSnippet('use Foobar;');
    $declaration = $declaration_block->getDeclarationStatements()[0]->getDeclarations()[0];
    $this->assertFalse($declaration->hasAlias());

    $alias = Token::identifier('TestAlias');
    $declaration->setAlias($alias);
    $this->assertTrue($declaration->hasAlias());
    $this->assertEquals('TestAlias', $declaration->getAlias()->getText());
    $this->assertEquals('Foobar as TestAlias', $declaration->getText());

    $declaration->setAlias('Overridden');
    $this->assertTrue($declaration->hasAlias());
    $this->assertEquals('Overridden', $declaration->getAlias()->getText());
    $this->assertEquals('Foobar as Overridden', $declaration->getText());

    $declaration->setAlias(NULL);
    $this->assertFalse($declaration->hasAlias());
    $this->assertEquals('Foobar', $declaration->getText());
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testSetAliasInvalidArgument() {
    /** @var \Pharborist\Namespaces\UseDeclarationBlockNode $declaration_block */
    $declaration_block = Parser::parseSnippet('use Cleese as Chapman;');
    $declaration = $declaration_block->getDeclarationStatements()[0]->getDeclarations()[0];
    $declaration->setAlias(3.141);
  }

  public function testCreate() {
    $declaration = UseDeclarationNode::create('Cleese as Chapman');
    $this->assertEquals('Cleese', $declaration->getName()->getText());
    $this->assertEquals('Chapman', $declaration->getAlias()->getText());
  }

}
