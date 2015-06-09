<?php
namespace Pharborist;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Functions\ParameterNode;
use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Objects\ClassMemberListNode;
use Pharborist\Objects\ClassMethodCallNode;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\ClassNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\ObjectMethodCallNode;

/**
 * Tests builder methods.
 */
class BuilderTest extends \PHPUnit_Framework_TestCase {
  public function testBuildClass() {
    $classNode = ClassNode::create('MyClass');
    $this->assertEquals('class MyClass {}', $classNode->getText());

    $classNode->setFinal(TRUE);
    $this->assertEquals('final class MyClass {}', $classNode->getText());

    $classNode->setFinal(FALSE);
    $this->assertEquals('class MyClass {}', $classNode->getText());

    $classNode->setName('MyTest');
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->setExtends('MyClass');
    $this->assertEquals('class MyTest extends MyClass {}', $classNode->getText());

    $classNode->setExtends('BaseClass');
    $this->assertEquals('class MyTest extends BaseClass {}', $classNode->getText());

    $classNode->setExtends(NULL);
    $this->assertNull($classNode->getExtends());
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->setImplements('MyInterface');
    $this->assertEquals('class MyTest implements MyInterface {}', $classNode->getText());

    $classNode->setImplements('Yai');
    $this->assertEquals('class MyTest implements Yai {}', $classNode->getText());

    $classNode->setImplements(NULL);
    $this->assertNull($classNode->getImplementList());
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->appendProperty('someProperty');
    $classNode->appendMethod('someMethod');

    $expected = <<<'EOF'
class MyTest {

  private $someProperty;

  public function someMethod() {
  }

}
EOF;
    $this->assertEquals($expected, $classNode->getText());

    $property = $classNode->getProperty('someProperty');
    $property->getClassMemberListNode()->setDocComment(DocCommentNode::create('Some property.'));

    $method = $classNode->getMethod('someMethod');
    $method->setDocComment(DocCommentNode::create('Some method.'));

    $expected = <<<'EOF'
class MyTest {

  /**
   * Some property.
   */
  private $someProperty;

  /**
   * Some method.
   */
  public function someMethod() {
  }

}
EOF;
    $this->assertEquals($expected, $classNode->getText());
  }

  public function testClassMethod() {
    $method = ClassMethodNode::create('someMethod');
    $this->assertEquals('public function someMethod() {}', $method->getText());

    $method->setVisibility(Token::_protected());
    $this->assertEquals('protected function someMethod() {}', $method->getText());

    $method->setFinal(TRUE);
    $this->assertEquals('final protected function someMethod() {}', $method->getText());

    $method->setFinal(FALSE);
    $this->assertEquals('protected function someMethod() {}', $method->getText());

    $method->setStatic(TRUE);
    $this->assertEquals('protected static function someMethod() {}', $method->getText());

    $method->setStatic(FALSE);
    $this->assertEquals('protected function someMethod() {}', $method->getText());
  }

  public function testClassProperty() {
    $property = ClassMemberListNode::create('someProperty');
    $this->assertEquals('private $someProperty;', $property->getText());

    $property->setVisibility(Token::_protected());
    $this->assertEquals('protected $someProperty;', $property->getText());
  }

  public function testObjectMethodCall() {
    $object = Token::variable('$object');
    $method_call = ObjectMethodCallNode::create($object, 'someMethod');
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $method_call);
    $this->assertEquals($object, $method_call->getObject());
    $this->assertEquals('$object', $method_call->getObject()->getText());
    $this->assertEquals('someMethod', $method_call->getMethodName()->getText());
    $this->assertCount(0, $method_call->getArguments());
    $var = Parser::parseExpression('$a');
    $method_call->appendArgument($var);
    $arg = $method_call->getArguments()[0];
    $this->assertInstanceOf('\Pharborist\Variables\VariableNode', $arg);
    $this->assertEquals('$a', $arg->getText());

    /** @var ParentNode $expression */
    $expression = Parser::parseExpression($method_call->getText());
    $expected = $expression->getTree();
    $actual = $method_call->getTree();
    $this->assertEquals($expected, $actual);
  }

  public function testClassMethodCall() {
    $method_call = ClassMethodCallNode::create('TestClass', 'someMethod');
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodCallNode', $method_call);
    $this->assertEquals('TestClass', $method_call->getClassName()->getText());
    $this->assertEquals('someMethod', $method_call->getMethodName()->getText());
    $this->assertCount(0, $method_call->getArguments());
    $var = Parser::parseExpression('$a');
    $method_call->appendArgument($var);
    $arg = $method_call->getArguments()[0];
    $this->assertInstanceOf('\Pharborist\Variables\VariableNode', $arg);
    $this->assertEquals('$a', $arg->getText());

    /** @var ParentNode $expression */
    $expression = Parser::parseExpression($method_call->getText());
    $expected = $expression->getTree();
    $actual = $method_call->getTree();
    $this->assertEquals($expected, $actual);
  }

  public function testChainMethodCall() {
    $object = Token::variable('$object');
    $method_call = ObjectMethodCallNode::create($object, 'someMethod');
    $chained_call = $method_call->appendMethodCall('chained');
    $this->assertEquals('$object->someMethod()', $chained_call->getObject()->getText());
    $this->assertEquals('chained', $chained_call->getMethodName()->getText());

    $source = <<<'EOF'
<?php
$object->someMethod();
EOF;
    $tree = Parser::parseSource($source);
    /** @var ExpressionStatementNode $expr_statement */
    $expr_statement = $tree->firstChild()->next();
    /** @var ObjectMethodCallNode $method_call */
    $method_call = $expr_statement->getExpression();
    $method_call->appendMethodCall('chained');
    $expected = <<<'EOF'
<?php
$object->someMethod()->chained();
EOF;
    $this->assertEquals($expected, $tree->getText());
  }

  public function testFunctionDeclaration() {
    $expected = 'function test($a, $b) {}';
    $function = FunctionDeclarationNode::create('test', ['$a', '$b']);
    $this->assertEquals($expected, $function->getText());

    $function = FunctionDeclarationNode::create('badoink');
    $function->appendParameter(ParameterNode::create('badonk'));
    $this->assertEquals('function badoink($badonk) {}', $function->getText());
  }

  public function testFunctionToMethod() {
    $class = <<<END
class DefaultController extends ControllerBase {}
END;

    $function = <<<'END'
function diff_diffs_overview($node) {
  drupal_set_title(t('Revisions for %title', array('%title' => $node->title)), PASS_THROUGH);
  if ($cond) {
    test();
  }
  return drupal_get_form('diff_node_revisions', $node);
}
END;

    $expected = <<<'END'
class DefaultController extends ControllerBase {

  public function diff_diffs_overview($node) {
    drupal_set_title(t('Revisions for %title', ['%title' => $node->title]), PASS_THROUGH);
    if ($cond) {
      test();
    }
    return drupal_get_form('diff_node_revisions', $node);
  }

}
END;

    /** @var ClassNode $class */
    $class = Parser::parseSnippet($class);
    $function = Parser::parseSnippet($function);
    $class->appendMethod(ClassMethodNode::fromFunction($function));
    $this->assertEquals($expected, $class->getText());
  }

  public function testNamespaceNode() {
    $ns = NamespaceNode::create('\Drupal\pantaloons');
    $this->assertInstanceOf('\Pharborist\Namespaces\NamespaceNode', $ns);
  }

  public function testBuildInterface() {
    $interfaceNode = InterfaceNode::create('MyInterface');
    $this->assertEquals('interface MyInterface {}', $interfaceNode->getText());

    $interfaceNode->setExtends(['BaseInterface', 'AnotherInterface']);
    $this->assertEquals('interface MyInterface extends BaseInterface, AnotherInterface {}', $interfaceNode->getText());

    $interfaceNode->setExtends(NULL);
    $this->assertEquals('interface MyInterface {}', $interfaceNode->getText());

    $interfaceNode->appendMethod('someMethod');

    $expected = <<<'EOF'
interface MyInterface {

  public function someMethod();

}
EOF;
    $this->assertEquals($expected, $interfaceNode->getText());
  }
}
