<?php
namespace Pharborist;

use Pharborist\Namespaces\UseDeclarationBlockNode;

class UseDeclarationStatementNodeTest extends \PHPUnit_Framework_TestCase {
  public function testImportClass() {
    /** @var UseDeclarationBlockNode $use_block */
    $use_block = Parser::parseSnippet('use MyNamespace\MyClass;');
    $use_declaration_statement = $use_block->getDeclarationStatements()[0];
    $this->assertCount(1, $use_declaration_statement->getDeclarations());
    $this->assertEquals($use_declaration_statement->getDeclarations(), $use_declaration_statement->getDeclarationList()->getItems());
    $this->assertTrue($use_declaration_statement->importsClass());
    $this->assertFalse($use_declaration_statement->importsConst());
    $this->assertFalse($use_declaration_statement->importsFunction());
    $this->assertTrue($use_declaration_statement->importsClass('MyNamespace\MyClass'));
    $this->assertFalse($use_declaration_statement->importsClass('MyNamespace\NotFound'));
  }

  public function testImportConst() {
    /** @var UseDeclarationBlockNode $use_block */
    $use_block = Parser::parseSnippet('use const MyNamespace\MY_CONST;');
    $use_declaration_statement = $use_block->getDeclarationStatements()[0];
    $this->assertCount(1, $use_declaration_statement->getDeclarations());
    $this->assertEquals($use_declaration_statement->getDeclarations(), $use_declaration_statement->getDeclarationList()->getItems());
    $this->assertTrue($use_declaration_statement->importsConst());
    $this->assertFalse($use_declaration_statement->importsClass());
    $this->assertFalse($use_declaration_statement->importsFunction());
    $this->assertTrue($use_declaration_statement->importsConst('MyNamespace\MY_CONST'));
    $this->assertFalse($use_declaration_statement->importsConst('MyNamespace\NOT_FOUND'));
  }

  public function testImportFunction() {
    /** @var UseDeclarationBlockNode $use_block */
    $use_block = Parser::parseSnippet('use function MyNamespace\test_func;');
    $use_declaration_statement = $use_block->getDeclarationStatements()[0];
    $this->assertCount(1, $use_declaration_statement->getDeclarations());
    $this->assertEquals($use_declaration_statement->getDeclarations(), $use_declaration_statement->getDeclarationList()->getItems());
    $this->assertTrue($use_declaration_statement->importsFunction());
    $this->assertFalse($use_declaration_statement->importsConst());
    $this->assertFalse($use_declaration_statement->importsClass());
    $this->assertTrue($use_declaration_statement->importsFunction('MyNamespace\test_func'));
    $this->assertFalse($use_declaration_statement->importsFunction('MyNamespace\not_found'));
  }
}
