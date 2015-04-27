<?php
namespace Pharborist;

class StatementBlockNodeTest extends \PHPUnit_Framework_TestCase {
  public function testAppendStatement() {
    /** @var \Pharborist\ControlStructures\IfNode $ifNode */
    $ifNode = Parser::parseSnippet('if (TRUE) { hello(); }');
    /** @var StatementBlockNode $statementBlock */
    $statementBlock = $ifNode->getThen();
    $this->assertInstanceOf('\Pharborist\StatementBlockNode', $statementBlock);
    $this->assertEquals('{ hello(); }', $statementBlock->getText());
    $statementBlock->appendStatement(Parser::parseSnippet('world();'));
    $this->assertEquals('{ hello(); world();}', $statementBlock->getText());
  }
}
