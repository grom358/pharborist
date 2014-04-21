<?php
namespace Pharborist;

class NameNodeTest extends \PHPUnit_Framework_TestCase {
  public function testUnqualified() {
    $snippet = 'namespace Top\Sub; test();';
    $tree = Parser::parseSnippet($snippet);
    /** @var ExpressionStatementNode $statement */
    $statement = $tree->lastChild();
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());
  }

  public function testRelative() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  function test() {
  }
}
namespace Top {
  Sub\test();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();
    /** @var ExpressionStatementNode $statement */
    $statement = $namespace->getBody()->getStatements()[0];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('Sub\test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());
  }

  public function testExplicitRelative() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  function test() {
  }
}
namespace Top {
  namespace\Sub\test();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();
    /** @var ExpressionStatementNode $statement */
    $statement = $namespace->getBody()->getStatements()[0];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('namespace\Sub\test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());
  }

  public function testAbsolute() {
    $snippet = 'Top\Sub\test();';
    $tree = Parser::parseSnippet($snippet);
    /** @var ExpressionStatementNode $statement */
    $statement = $tree->lastChild();
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('Top\Sub\test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());
  }

  public function testAlias() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  use A\B as C;
  C\test();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();
    /** @var ExpressionStatementNode $statement */
    $statement = $namespace->getBody()->getStatements()[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('C\test', $name->getText());
    $this->assertEquals('\A\B\test', $name->getAbsolutePath());
  }
}
