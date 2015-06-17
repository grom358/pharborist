<?php
namespace Pharborist;

use Pharborist\Functions\ParameterNode;

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
    $this->assertTrue($parameter->isReference());
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
    $this->assertFalse($parameter->isReference());
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
namespace MyNamespace;

use MyNamespace\MyClass;
use MyNamespace\SomeClass as TestClass;

/**
 * @param MyClass $a
 *   A test parameter.
 * @param TestClass $b
 *   Parameter using alias.
 * @param \MyNamespace\FullClass $c
 *   Parameter using fully qualified name.
 * @param object $d
 *   Parameter with fully qualified type hint.
 * @param object $e
 *   Parameter with relative type hint.
 * @param array $data
 *   An array parameter.
 * @param callable $callback
 *   A callable parameter.
 * @param int $num
 *   An integer parameter.
 * @param Node[] $nodes
 *   Test array type overriding type hint for arrays.
 */
function foo($a, $b, $c, \MyNamespace\FullClass $d, RelativeClass $e, $data, $callback, $num, array $nodes, $unknown) {
  $a = new stdClass();
}
EOF;
    $tree = Parser::parseSource($source);

    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $tree->find(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode'))[0];

    $this->assertEquals(['\MyNamespace\MyClass'], $function->getParameter(0)->getTypes());
    $this->assertEquals(['\MyNamespace\SomeClass'], $function->getParameter(1)->getTypes());
    $this->assertEquals(['\MyNamespace\FullClass'], $function->getParameter(2)->getTypes());
    $this->assertEquals(['\MyNamespace\FullClass'], $function->getParameter(3)->getTypes());
    $this->assertEquals(['\MyNamespace\RelativeClass'], $function->getParameter(4)->getTypes());
    $this->assertEquals(['array'], $function->getParameter(5)->getTypes());
    $this->assertEquals(['callable'], $function->getParameter(6)->getTypes());
    $this->assertEquals(['int'], $function->getParameter(7)->getTypes());
    $this->assertEquals(['\MyNamespace\Node[]'], $function->getParameter(8)->getTypes());
    $this->assertEquals(['mixed'], $function->getParameter(9)->getTypes());
    $this->assertFalse($function->getParameter(9)->hasDocTypes());
  }

  public function testMatchReflector() {
    // @TODO Reflect on a function we define so we can more fully test this
    $reflector = (new \ReflectionFunction('array_walk'))->getParameters()[0];
    $node = ParameterNode::create('array')->matchReflector($reflector);

    $this->assertInstanceOf('\Pharborist\TokenNode', $node->getReference());
    $this->assertSame('&', $node->getReference()->getText());
    $this->assertNull($node->getValue());
  }

  public function testTypeHint() {
    $source = <<<'EOF'
<?php
use MyNamespace\MyClass;

function foo($a, array $b) {
}
EOF;
    $tree = Parser::parseSource($source);

    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $tree->children(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode'))[0];

    $parameter = $function->getParameter(0);
    $this->assertInstanceOf('Pharborist\Functions\FunctionDeclarationNode', $parameter->getFunction());
    $this->assertNull($parameter->getTypeHint());
    $parameter->setTypeHint('array');
    $this->assertEquals('array', $parameter->getTypeHint()->getText());

    $parameter = $function->getParameter(1);
    $this->assertInstanceOf('Pharborist\Functions\FunctionDeclarationNode', $parameter->getFunction());
    $this->assertEquals('array', $parameter->getTypeHint()->getText());
    $parameter->setTypeHint('callable');
    $this->assertEquals('callable', $parameter->getTypeHint()->getText());
    $this->assertEquals('callable $b', $parameter->getText());
    $parameter->setTypeHint('MyClass');
    $this->assertEquals('\MyNamespace\MyClass', $parameter->getTypeHint()->getAbsolutePath());
    $this->assertEquals('MyClass $b', $parameter->getText());
    $parameter->setTypeHint(NULL);
    $this->assertNull($parameter->getTypeHint());
  }

  /**
   * @requires PHP 5.6
   */
  public function testVariadic() {
    $source = <<<'EOF'
<?php
function foo($args) {
}
EOF;
    $tree = Parser::parseSource($source);

    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $tree->children(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode'))[0];

    $parameter = $function->getParameter(0);
    $this->assertFalse($parameter->isVariadic());
    $parameter->setVariadic(TRUE);
    $this->assertTrue($parameter->isVariadic());
    $this->assertEquals('...$args', $parameter->getText());
  }
}
