<?php
namespace Pharborist;

/**
 * Tests the various filters provided by the filter factory.
 */
class FilterTest extends \PHPUnit_Framework_TestCase {
  public function testIsInstanceOf() {
    $source = <<<'END'
<?php
$foo = 'baz';
function a() {}
class B {}
END;
    $doc = Parser::parseSource($source);
    $stuff = $doc->find(Filter::isInstanceOf('\Pharborist\Variables\VariableNode', '\Pharborist\Functions\FunctionDeclarationNode', '\Pharborist\Objects\ClassNode'));
    $this->assertCount(3, $stuff);
    $this->assertInstanceOf('\Pharborist\Variables\VariableNode', $stuff[0]);
    $this->assertEquals('$foo', $stuff[0]);
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $stuff[1]);
    $this->assertEquals('function a() {}', $stuff[1]);
    $this->assertInstanceOf('\Pharborist\Objects\ClassNode', $stuff[2]);
    $this->assertEquals('class B {}', $stuff[2]);
  }

  public function testIsFunction() {
    $source = <<<'END'
<?php
function foo() {}
function bar() {}
function baz() {}
END;
    /** @var \Pharborist\Functions\FunctionDeclarationNode[] $functions */
    $functions = Parser::parseSource($source)->find(Filter::isFunction('foo', 'bar'));
    $this->assertCount(2, $functions);
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $functions[0]);
    $this->assertEquals('foo', $functions[0]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $functions[1]);
    $this->assertEquals('bar', $functions[1]->getName());
  }

  public function testIsFunctionCall() {
    $source = <<<'END'
<?php
echo strrev("Foobaz");
echo strlen("I am a banana!");
echo strrev("Foobar");
echo md5("Werd up.");
END;
    /** @var \Pharborist\Functions\FunctionCallNode[] $function_calls */
    $function_calls = Parser::parseSource($source)->find(Filter::isFunctionCall('strrev', 'strlen'));
    $this->assertCount(3, $function_calls);
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[0]);
    $this->assertEquals('strrev', $function_calls[0]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[1]);
    $this->assertEquals('strlen', $function_calls[1]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[2]);
    $this->assertEquals('strrev', $function_calls[2]->getName());
  }

  public function testIsClass() {
    $source = <<<'END'
<?php
class A {}
class B {}
class C {}
END;
    /** @var \Pharborist\Objects\ClassNode[] $classes */
    $classes = Parser::parseSource($source)->find(Filter::isClass('A', 'B'));
    $this->assertCount(2, $classes);
    $this->assertInstanceOf('\Pharborist\Objects\ClassNode', $classes[0]);
    $this->assertEquals('A', $classes[0]->getName());
    $this->assertInstanceOf('\Pharborist\Objects\ClassNode', $classes[1]);
    $this->assertEquals('B', $classes[1]->getName());
  }

  public function testIsClassMethodCall() {
    $source = <<<'END'
<?php
use MyNamespace\Test;

A::test();
MyNamespace\Test::method();
Test::method();
END;
    /** @var \Pharborist\Objects\ClassMethodCallNode[] $method_calls */
    $method_calls = Parser::parseSource($source)->find(Filter::isClassMethodCall('\A', 'test'));
    $this->assertCount(1, $method_calls);
    $method_call = $method_calls[0];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodCallNode', $method_call);
    $this->assertEquals('\A', $method_call->getClassName()->getAbsolutePath());
    $this->assertEquals('test', $method_call->getMethodName()->getText());
    $method_calls = Parser::parseSource($source)->find(Filter::isClassMethodCall('\MyNamespace\Test', 'method'));
    $this->assertCount(2, $method_calls);
    $method_call = $method_calls[0];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodCallNode', $method_call);
    $this->assertEquals('\MyNamespace\Test', $method_call->getClassName()->getAbsolutePath());
    $this->assertEquals('method', $method_call->getMethodName()->getText());
  }

  public function testAny() {
    $pass = function () { return TRUE; };
    $fail = function () { return FALSE; };

    $filter = Filter::any([$fail]);
    $this->assertFalse($filter(NULL));

    $filter = Filter::any([$fail, $pass]);
    $this->assertTrue($filter(NULL));
  }

  public function testAll() {
    $pass = function () { return TRUE; };
    $fail = function () { return FALSE; };

    $filter = Filter::all([$pass]);
    $this->assertTrue($filter(NULL));

    $filter = Filter::all([$pass, $fail]);
    $this->assertFalse($filter(NULL));
  }
}
