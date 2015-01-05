<?php
namespace Pharborist;

use Pharborist\Functions\FunctionCallNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Objects\ClassMethodCallNode;
use Pharborist\Objects\NewNode;

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
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('test', $name->getText());
    $this->assertEquals('\Top\Sub\test', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\Top\Sub\F', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\Top\Sub\B', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[5];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\C\E', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\Top\Sub\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
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
  new F\G();
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('my\foo', $name->getText());
    $this->assertEquals('\Top\Sub\my\foo', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('B\foo', $name->getText());
    $this->assertEquals('\Top\Sub\B\foo', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('A\B', $name->getText());
    $this->assertEquals('\Top\Sub\A\B', $name->getAbsolutePath());

    $statement = $statements[4];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('F\G', $name->getText());
    $this->assertEquals('\C\E\G', $name->getAbsolutePath());
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
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\foo', $name->getText());
    $this->assertEquals('\foo', $name->getAbsolutePath());

    $statement = $statements[2];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[3];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\D', $name->getText());
    $this->assertEquals('\D', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\F', $name->getText());
    $this->assertEquals('\F', $name->getAbsolutePath());

    $statement = $statements[5];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\B\foo', $name->getText());
    $this->assertEquals('\B\foo', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isAbsolute());
    $this->assertEquals('\A\B', $name->getText());
    $this->assertEquals('\A\B', $name->getAbsolutePath());
  }

  public function testRelative() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  new namespace\Level\MyClass();
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();
    /** @var ExpressionStatementNode $statement */
    $statement = $statements[0];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isRelative());
    $this->assertEquals('namespace\Level\MyClass', $name->getText());
    $this->assertEquals('\Top\Sub\Level\MyClass', $name->getAbsolutePath());
  }

  public function testCreate() {
    $namespace = NameNode::create('MyNamespace');
    $this->assertCount(1, $namespace->children());
    $this->assertEquals('MyNamespace', $namespace->firstChild()->getText());

    $namespace = NameNode::create('Top\Sub');
    /** @var Node[] $children */
    $children = $namespace->children();
    $this->assertCount(3, $children);
    $this->assertEquals('Top', $children[0]->getText());
    $this->assertEquals('\\', $children[1]->getText());
    $this->assertEquals('Sub', $children[2]->getText());

    $namespace = NameNode::create('\Top\Sub');
    $children = $namespace->children();
    $this->assertCount(4, $children);
    $this->assertEquals('\\', $children[0]->getText());
    $this->assertEquals('Top', $children[1]->getText());
    $this->assertEquals('\\', $children[2]->getText());
    $this->assertEquals('Sub', $children[3]->getText());
  }

  public function testFunction() {
    $snippet = <<<'EOF'
namespace Test {
  use function MyNamespace\test;
  use function MyNamespace\some_func as my_func;
  test();
  my_func();
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[2];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('\MyNamespace\test', $name->getAbsolutePath());

    $statement = $statements[3];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertEquals('\MyNamespace\some_func', $name->getAbsolutePath());
  }

  public function testConst() {
    $snippet = <<<'EOF'
namespace Test {
  use const MyNamespace\TEST;
  use const MyNamespace\SOME_CONST as MY_CONST;
  $a = TEST;
  $a = MY_CONST;
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[2];
    /** @var \Pharborist\Operators\AssignNode $assignment */
    $assignment = $statement->getExpression();
    /** @var \Pharborist\Constants\ConstantNode $const */
    $const = $assignment->getRightOperand();
    $this->assertEquals('\MyNamespace\TEST', $const->getConstantName()->getAbsolutePath());

    $statement = $statements[3];
    $assignment = $statement->getExpression();
    $const = $assignment->getRightOperand();
    $this->assertEquals('\MyNamespace\SOME_CONST', $const->getConstantName()->getAbsolutePath());

    $snippet = <<<'EOF'
namespace Test {
  $a = TEST;
  $a = MY_CONST;
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[0];
    /** @var \Pharborist\Operators\AssignNode $assignment */
    $assignment = $statement->getExpression();
    /** @var \Pharborist\Constants\ConstantNode $const */
    $const = $assignment->getRightOperand();
    $this->assertEquals('\Test\TEST', $const->getConstantName()->getAbsolutePath());

    $statement = $statements[1];
    $assignment = $statement->getExpression();
    $const = $assignment->getRightOperand();
    $this->assertEquals('\Test\MY_CONST', $const->getConstantName()->getAbsolutePath());
  }

  public function testGlobal() {
    $snippet = <<<'EOF'
namespace {
  use B\D, C\E as F;
  test();
  F();
  new B();
  new D();
  new F();
  B::foo();
  D::foo();
  my\foo();
  B\foo();
  A\B::foo();
  new F\G();
  new namespace\Level\MyClass();
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('test', $name->getText());
    $this->assertEquals('\test', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\F', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[5];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\C\E', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[8];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('my\foo', $name->getText());
    $this->assertEquals('\my\foo', $name->getAbsolutePath());

    $statement = $statements[9];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('B\foo', $name->getText());
    $this->assertEquals('\B\foo', $name->getAbsolutePath());

    $statement = $statements[10];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('A\B', $name->getText());
    $this->assertEquals('\A\B', $name->getAbsolutePath());

    $statement = $statements[11];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('F\G', $name->getText());
    $this->assertEquals('\C\E\G', $name->getAbsolutePath());

    $statement = $statements[12];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isRelative());
    $this->assertEquals('namespace\Level\MyClass', $name->getText());
    $this->assertEquals('\Level\MyClass', $name->getAbsolutePath());
  }

  public function testRoot() {
    $source = <<<'EOF'
<?php
use B\D, C\E as F;
test();
F();
new B();
new D();
new F();
B::foo();
D::foo();
my\foo();
B\foo();
A\B::foo();
new F\G();
new namespace\Level\MyClass();
EOF;
    /** @var RootNode $namespace */
    $root = Parser::parseSource($source);

    /** @var StatementNode[] $statements */
    $statements = $root->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[1];
    /** @var FunctionCallNode $function_call */
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('test', $name->getText());
    $this->assertEquals('\test', $name->getAbsolutePath());

    $statement = $statements[2];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\F', $name->getAbsolutePath());

    $statement = $statements[3];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[4];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[5];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('F', $name->getText());
    $this->assertEquals('\C\E', $name->getAbsolutePath());

    $statement = $statements[6];
    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('B', $name->getText());
    $this->assertEquals('\B', $name->getAbsolutePath());

    $statement = $statements[7];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isUnqualified());
    $this->assertEquals('D', $name->getText());
    $this->assertEquals('\B\D', $name->getAbsolutePath());

    $statement = $statements[8];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('my\foo', $name->getText());
    $this->assertEquals('\my\foo', $name->getAbsolutePath());

    $statement = $statements[9];
    $function_call = $statement->getExpression();
    $name = $function_call->getName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('B\foo', $name->getText());
    $this->assertEquals('\B\foo', $name->getAbsolutePath());

    $statement = $statements[10];
    $class_method_call = $statement->getExpression();
    $name = $class_method_call->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('A\B', $name->getText());
    $this->assertEquals('\A\B', $name->getAbsolutePath());

    $statement = $statements[11];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isQualified());
    $this->assertEquals('F\G', $name->getText());
    $this->assertEquals('\C\E\G', $name->getAbsolutePath());

    $statement = $statements[12];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isRelative());
    $this->assertEquals('namespace\Level\MyClass', $name->getText());
    $this->assertEquals('\Level\MyClass', $name->getAbsolutePath());
  }

  public function testPath() {
    $snippet = <<<'EOF'
namespace Top\Sub {
  new \IsGlobal();
  new NonGlobal();
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);
    $this->assertEquals('\\', $namespace->getName()->getParentPath());

    /** @var StatementNode[] $statements */
    $statements = $namespace->getBody()->getStatements();

    /** @var ExpressionStatementNode $statement */
    $statement = $statements[0];
    /** @var NewNode $new */
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertTrue($name->isGlobal());
    $this->assertEquals('\\', $name->getParentPath());
    $this->assertEquals('IsGlobal', $name->getBaseName());

    $statement = $statements[1];
    $new = $statement->getExpression();
    $name = $new->getClassName();
    $this->assertFalse($name->isGlobal());
    $this->assertEquals('\Top\Sub\\', $name->getParentPath());
    $this->assertEquals('NonGlobal', $name->getBaseName());
  }
}
