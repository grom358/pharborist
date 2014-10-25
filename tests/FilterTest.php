<?php
namespace Pharborist;

/**
 * Tests the various filters provided by the filter factory.
 */
class FilterTest extends \PHPUnit_Framework_TestCase {
  public function testIsInstanceOf() {
    $doc = <<<'END'
<?php
$foo = 'baz';
function a() {}
class B {}
END;
    $doc = Parser::parseSource($doc);
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
    $doc = <<<'END'
<?php
function foo() {}
function bar() {}
function baz() {}
END;
    $functions = Parser::parseSource($doc)->find(Filter::isFunction('foo', 'bar'));
    $this->assertCount(2, $functions);
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $functions[0]);
    $this->assertEquals('foo', $functions[0]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $functions[1]);
    $this->assertEquals('bar', $functions[1]->getName());
  }

  public function testIsFunctionCall() {
    $doc = <<<'END'
<?php
echo strrev("Foobaz");
echo strlen("I am a banana!");
echo strrev("Foobar");
echo md5("Werd up.");
END;
    $function_calls = Parser::parseSource($doc)->find(Filter::isFunctionCall('strrev', 'strlen'));
    $this->assertCount(3, $function_calls);
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[0]);
    $this->assertEquals('strrev', $function_calls[0]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[1]);
    $this->assertEquals('strlen', $function_calls[1]->getName());
    $this->assertInstanceOf('\Pharborist\Functions\FunctionCallNode', $function_calls[2]);
    $this->assertEquals('strrev', $function_calls[2]->getName());
  }

  public function testIsClass() {
    $doc = <<<'END'
<?php
class A {}
class B {}
class C {}
END;
    $classes = Parser::parseSource($doc)->find(Filter::isClass('A', 'B'));
    $this->assertCount(2, $classes);
    $this->assertInstanceOf('\Pharborist\Objects\ClassNode', $classes[0]);
    $this->assertEquals('A', $classes[0]->getName());
    $this->assertInstanceOf('\Pharborist\Objects\ClassNode', $classes[1]);
    $this->assertEquals('B', $classes[1]->getName());
  }
}
