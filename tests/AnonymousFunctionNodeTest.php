<?php
namespace Pharborist;

use Pharborist\Functions\AnonymousFunctionNode;

class AnonymousFunctionNodeTest extends \PHPUnit_Framework_TestCase {
  public function testReference() {
    /** @var AnonymousFunctionNode $func */
    $func = Parser::parseExpression('function () {}');
    $this->assertNull($func->getReference());
    $func->setReference(TRUE);
    $this->assertNotNull($func->getReference());
    $this->assertEquals('&', $func->getReference()->getText());
    $this->assertEquals('function &() {}', $func->getText());

    $func->setReference(FALSE);
    $this->assertNull($func->getReference());
    $this->assertEquals('function () {}', $func->getText());
  }

  public function testLexicalVariables() {
    /** @var AnonymousFunctionNode $func */
    $func = Parser::parseExpression('function () {}');
    $func->appendLexicalVariable(Token::variable('$a'));
    $this->assertTrue($func->hasLexicalVariables());
    $lexical_variables = $func->getLexicalVariables();
    $this->assertCount(1, $func->getLexicalVariables());
    $this->assertEquals('$a', $lexical_variables[0]->getText());
    $this->assertEquals($lexical_variables, $func->getLexicalVariableList()->getItems());
    $this->assertEquals('function () use ($a) {}', $func->getText());
    $this->assertEquals('use', $func->getLexicalUse()->getText());
    $this->assertEquals('(', $func->getLexicalOpenParen());
    $this->assertNotSame($func->getOpenParen(), $func->getLexicalOpenParen());
    $this->assertEquals(')', $func->getLexicalCloseParen());
    $this->assertNotSame($func->getCloseParen(), $func->getLexicalCloseParen());

    $func->prependLexicalVariable(Token::variable('$before'));
    $lexical_variables = $func->getLexicalVariables();
    $this->assertCount(2, $func->getLexicalVariables());
    $this->assertEquals('$before', $lexical_variables[0]->getText());
    $this->assertEquals('$a', $lexical_variables[1]->getText());
    $this->assertEquals($lexical_variables, $func->getLexicalVariableList()->getItems());
    $this->assertEquals('function () use ($before, $a) {}', $func->getText());

    $func->insertLexicalVariable(Token::variable('$middle'), 1);
    $lexical_variables = $func->getLexicalVariables();
    $this->assertCount(3, $func->getLexicalVariables());
    $this->assertEquals('$before', $lexical_variables[0]->getText());
    $this->assertEquals('$middle', $lexical_variables[1]->getText());
    $this->assertEquals('$a', $lexical_variables[2]->getText());
    $this->assertEquals($lexical_variables, $func->getLexicalVariableList()->getItems());
    $this->assertEquals('function () use ($before, $middle, $a) {}', $func->getText());

    $func->clearLexicalVariables();
    $this->assertFalse($func->hasLexicalVariables());
    $this->assertEquals('function () {}', $func->getText());
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testInsertNegativeIndex() {
    /** @var AnonymousFunctionNode $func */
    $func = Parser::parseExpression('function () use ($b) {}');
    $func->insertLexicalVariable(Token::variable('$a'), -1);
  }

  /**
   * @expectedException \OutOfBoundsException
   */
  public function testInsertOutOfBoundsEmpty() {
    /** @var AnonymousFunctionNode $func */
    $func = Parser::parseExpression('function () {}');
    $func->insertLexicalVariable(Token::variable('$a'), 1);
  }
}
