<?php
namespace Pharborist;

class NameNodeTest extends \PHPUnit_Framework_TestCase {
  public function testUnqualified() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  use B\D, C\E as F;
  test();
  F();
  new B();
  new D();
  new F();
  B::foo();
  D::foo();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\Top\Sub\F', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\Top\Sub\B', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[5];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\C\E', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\Top\Sub\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());
  }

  public function testQualified() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  use B\D, C\E as F;
  my\foo();
  B\foo();
  A\B::foo();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('my\foo', $name->getText());
    $this->assertEquals('\Top\Sub\my\foo', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('B\foo', $name->getText());
    $this->assertEquals('\Top\Sub\B\foo', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertEquals('A\B', $name->getText());
    $this->assertEquals('\Top\Sub\A\B', $name->getAbsolutePath());
  }

  public function testFullyQualified() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  use B\D, C\E as F;
  \foo();
  new \B();
  new \D();
  new \F();
  \B\foo();
  \B::foo();
  \A\B::foo();
}
EOF;
    $tree = Parser::parseSnippet($snippet);
    /** @var NamespaceNode $namespace */
    $namespace = $tree->lastChild();

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('\foo', $name->getText());
    $this->assertEquals('\foo', $name->getAbsolutePath());

    $statement = $statements[2];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('\B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[3];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('\D', $name->getText());
    $this->assertEquals('\D', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertEquals('\F', $name->getText());
    $this->assertEquals('\F', $name->getAbsolutePath());

    $statement = $statements[5];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('\B\foo', $name->getText());
    $this->assertEquals('\B\foo', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertEquals('\B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertEquals('\A\B', $name->getText());
    $this->assertEquals('\A\B', $name->getAbsolutePath());
  }
}
