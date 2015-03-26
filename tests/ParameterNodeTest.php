<?php
namespace Pharborist;

/**
 * Tests various methods of ParameterNode.
 */
class ParameterNodeTest extends \PHPUnit_Framework_TestCase {
  public function testParameterNode() {
    $source = <<<'EOF'
<?php
use MyNamespace\MyClass;
use MyNamespace\SomeClass as TestClass;

/**
 * @param MyClass $a
 *   A test parameter.
 * @param TestClass $c
 *   Parameter using alias.
 * @param array $data
 *   An array parameter.
 * @param callable $callback
 *   A callable parameter.
 */
function foo(MyClass &$a = NULL, TestClass $c, array $data, callable $callback) {
  $a = new stdClass();
}
EOF;
    $tree = Parser::parseSource($source);

    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $tree->children(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode'))[0];
    $parameter = $function->getParameter(0);

    $this->assertInstanceOf('Pharborist\Functions\FunctionDeclarationNode', $parameter->getFunction());
    $this->assertEquals('MyClass', $parameter->getTypeHint()->getText());
    $this->assertInstanceOf('Pharborist\TokenNode', $parameter->getReference());
    $this->assertFalse($parameter->isRequired());
    $this->assertTrue($parameter->isOptional());
    $this->assertEquals('$a', $parameter->getVariable()->getText());
    $this->assertEquals('a', $parameter->getName());
    $this->assertEquals('NULL', $parameter->getValue()->getText());
    $this->assertNotNull($parameter->getValue());
    $this->assertTrue($parameter->isOptional());
    $this->assertFalse($parameter->isRequired());
    $param_tag = $parameter->getDocBlockTag();
    $this->assertNotNull($param_tag);
    $this->assertEquals('$a', $param_tag->getVariableName());
    $types = $param_tag->getTypes();
    $this->assertCount(1, $types);
    $this->assertEquals('\MyNamespace\MyClass', $types[0]);
    $this->assertEquals('\MyNamespace\MyClass', $param_tag->getType());
    $this->assertFalse($param_tag->isVariadic());
    $this->assertEquals('A test parameter.', $param_tag->getDescription());
    $this->assertEquals($types, $parameter->getTypes());

    $parameter->setName('b', TRUE);
    $this->assertEquals('b', $parameter->getName());
    $this->assertEquals('MyClass &$b = NULL', $parameter->getText());
    $variables = $function->getBody()
      ->find(Filter::isInstanceOf('Pharborist\Variables\VariableNode'));
    foreach ($variables as $variable) {
      $this->assertEquals('$b', $variable->getText());
    }

    $parameter->setValue(NULL);
    $this->assertNull($parameter->getValue());
    $this->assertFalse($parameter->isOptional());
    $this->assertTrue($parameter->isRequired());

    $parameter = $function->getParameter(1);
    $this->assertEquals('c', $parameter->getName());
    $this->assertNull($parameter->getValue());
    $this->assertFalse($parameter->isOptional());
    $this->assertTrue($parameter->isRequired());
    $param_tag = $parameter->getDocBlockTag();
    $this->assertNotNull($param_tag);
    $this->assertEquals('$c', $param_tag->getVariableName());
    $types = $param_tag->getTypes();
    $this->assertCount(1, $types);
    $this->assertEquals('\MyNamespace\SomeClass', $types[0]);
    $this->assertEquals('\MyNamespace\SomeClass', $param_tag->getType());
    $this->assertFalse($param_tag->isVariadic());
    $this->assertEquals('Parameter using alias.', $param_tag->getDescription());
    $this->assertEquals($types, $parameter->getTypes());

    $parameter = $function->getParameter(2);
    $this->assertEquals('data', $parameter->getName());
    $this->assertEquals('array', $parameter->getTypeHint()->getText());
    $this->assertNull($parameter->getValue());
    $this->assertFalse($parameter->isOptional());
    $this->assertTrue($parameter->isRequired());
    $param_tag = $parameter->getDocBlockTag();
    $this->assertNotNull($param_tag);
    $this->assertEquals('$data', $param_tag->getVariableName());
    $types = $param_tag->getTypes();
    $this->assertCount(1, $types);
    $this->assertEquals('array', $types[0]);
    $this->assertEquals('array', $param_tag->getType());
    $this->assertFalse($param_tag->isVariadic());
    $this->assertEquals('An array parameter.', $param_tag->getDescription());
    $this->assertEquals($types, $parameter->getTypes());

    $parameter = $function->getParameter(3);
    $this->assertEquals('callback', $parameter->getName());
    $this->assertEquals('callable', $parameter->getTypeHint()->getText());
    $this->assertNull($parameter->getValue());
    $this->assertFalse($parameter->isOptional());
    $this->assertTrue($parameter->isRequired());
    $param_tag = $parameter->getDocBlockTag();
    $this->assertNotNull($param_tag);
    $this->assertEquals('$callback', $param_tag->getVariableName());
    $types = $param_tag->getTypes();
    $this->assertCount(1, $types);
    $this->assertEquals('callable', $types[0]);
    $this->assertEquals('callable', $param_tag->getType());
    $this->assertFalse($param_tag->isVariadic());
    $this->assertEquals('A callable parameter.', $param_tag->getDescription());
    $this->assertEquals($types, $parameter->getTypes());
  }

  public function testDocCommentTypes() {
    $source = <<<'EOF'
<?php
use MyNamespace\MyClass;
use MyNamespace\SomeClass as TestClass;

/**
 * @param MyClass $a
 *   A test parameter.
 * @param TestClass $b
 *   Parameter using alias.
 * @param array $data
 *   An array parameter.
 * @param callable $callback
 *   A callable parameter.
 * @param int $num
 *   An integer parameter.
 */
function foo($a, $b, $data, $callback, $num, $unknown) {
  $a = new stdClass();
}
EOF;
    $tree = Parser::parseSource($source);

    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $tree->children(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode'))[0];

    $this->assertEquals(['\MyNamespace\MyClass'], $function->getParameter(0)->getTypes());
    $this->assertEquals(['\MyNamespace\SomeClass'], $function->getParameter(1)->getTypes());
    $this->assertEquals(['array'], $function->getParameter(2)->getTypes());
    $this->assertEquals(['callable'], $function->getParameter(3)->getTypes());
    $this->assertEquals(['int'], $function->getParameter(4)->getTypes());
    $this->assertEquals(['mixed'], $function->getParameter(5)->getTypes());
  }
}
