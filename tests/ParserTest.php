<?php
namespace Pharborist;

/**
 * Tests Phaborist\Parser.
 */
class ParserTest extends \PHPUnit_Framework_TestCase {

  /**
   * Tests \Pharborist\Parser::parseFile().
   */
  public function testParseFile() {
    // Test with a real file.
    $tree = Parser::parseFile(__DIR__ . '/files/basic.php');
    $this->assertInstanceOf('\Pharborist\Node', $tree);
    $this->assertSame(count($tree->filter('\Pharborist\FunctionDeclarationNode')), 1);
    // Test with a non-existant file.
    $tree = Parser::parseFile('no-such-file.php');
    $this->assertFalse($tree);
  }

  /**
   * Helper function to parse a snippet.
   */
  public function parseSnippet($snippet, $expected_type) {
    $tree = Parser::parseSnippet($snippet);
    $source = (string) $tree;
    $this->assertEquals($snippet, $source);
    $first_child = $tree->children[0];
    $this->assertInstanceOf($expected_type, $first_child);
    return $first_child;
  }

  /**
   * Test parsing empty source file.
   */
  public function testParseEmpty() {
    $tree = Parser::parseSource('');
    $this->assertEmpty($tree->children);
  }

  /**
   * Test parsing php file with no code.
   */
  public function testParseBlank() {
    $tree = Parser::parseSource("<?php\n");
    $this->assertNotEmpty($tree->children);
    $this->assertInstanceOf('\Pharborist\TokenNode', $tree->children[0]);
    /** @var TokenNode $child */
    $child = $tree->children[0];
    $this->assertEquals(T_OPEN_TAG, $child->getType());
  }

  /**
   * Test parsing namespace.
   */
  public function testNamespace() {
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace MyNamespace\Test ;', '\Pharborist\NamespaceNode');
    $this->assertEquals('MyNamespace\Test', $namespace_node->name);

    // Test with body
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace MyNamespace\Test\Body { }', '\Pharborist\NamespaceNode');
    $this->assertEquals('MyNamespace\Test\Body', $namespace_node->name);
    $this->assertNotNull($namespace_node->body);

    // Test global
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace { }', '\Pharborist\NamespaceNode');
    $this->assertNull($namespace_node->name);
    $this->assertNotNull($namespace_node->body);
  }

  /**
   * Test parsing use declarations.
   */
  public function testUseDeclaration() {
    /** @var UseDeclarationStatementNode $use_declaration_statement */
    $use_declaration_statement = $this->parseSnippet(
      'use MyNamespace\MyClass as MyAlias ;',
      '\Pharborist\UseDeclarationStatementNode'
    );
    $use_declaration = $use_declaration_statement->declarations[0];
    $this->assertEquals('MyNamespace\MyClass', (string) $use_declaration->namespacePath);
    $this->assertEquals('MyAlias', (string) $use_declaration->alias);
    $this->assertEquals('MyNamespace\MyClass as MyAlias', (string) $use_declaration);
  }

  /**
   * Test parsing function declaration.
   */
  public function testFunctionDeclaration() {
    /** @var FunctionDeclarationNode $function_declaration */
    $function_declaration = $this->parseSnippet(
      'function my_func(array $a, callable $b, namespace\Test $c, \MyNamespace\Test $d, $e = 1) { }',
      '\Pharborist\FunctionDeclarationNode'
    );
    $this->assertEquals('my_func', $function_declaration->name);
    $parameter = $function_declaration->parameters[0];
    $this->assertEquals('$a', $parameter->name);
    $this->assertEquals('array', $parameter->classType);
    $parameter = $function_declaration->parameters[1];
    $this->assertEquals('$b', $parameter->name);
    $this->assertEquals('callable', $parameter->classType);
    //@todo
  }

  /**
   * Test parsing const declaration.
   */
  public function testConstDeclaration() {
    /** @var ConstantDeclarationStatementNode $const_declaration_list */
    $const_declaration_list = $this->parseSnippet('const MyConst = 1;', '\Pharborist\ConstantDeclarationStatementNode');
    $const_declaration = $const_declaration_list->declarations[0];
    $this->assertEquals('MyConst', $const_declaration->name);
    $this->assertEquals('1', $const_declaration->value);
  }

  /**
   * Test parsing top level halt compiler.
   */
  public function testHaltCompiler() {
    $node = $this->parseSnippet('__halt_compiler();', '\Pharborist\HaltCompilerNode');
    $this->assertEquals("__halt_compiler();", $node);
  }

  /**
   * Test inner halt compiler is an error.
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage __halt_compiler can only be used from the outermost scope
   */
  public function testInnerHaltCompiler() {
    $this->parseSnippet("{ __halt_compiler(); }", '\Pharborist\HaltCompilerNode');
  }

  /**
   * Test parsing a class declaration.
   * @covers Pharborist\Parser
   */
  public function testClassDeclaration() {
    $snippet = <<<'EOF'
abstract class MyClass extends ParentClass implements SomeInterface, AnotherInterface {
  const MY_CONST = 1;
  public $publicProperty = 1;
  protected $protectedProperty;
  private $privateProperty;
  static public $classProperty;
  var $backwardsCompatibility;

  public function myMethod() {
  }

  final public function noOverride() {
  }

  static public function classMethod() {
  }

  abstract public function mustImplement();

  use A, B, C {
    B::smallTalk insteadof A;
    A::bigTalk insteadof B, C;
    B::bigTalk as talk;
  }
}
EOF;
    /** @var ClassNode $class_declaration */
    $class_declaration = $this->parseSnippet($snippet, '\Pharborist\ClassNode');
    $this->assertEquals('MyClass', (string) $class_declaration->name);
    $this->assertEquals('ParentClass', (string) $class_declaration->extends);
    $this->assertEquals('SomeInterface', (string) $class_declaration->implements[0]);
    $this->assertEquals('AnotherInterface', (string) $class_declaration->implements[1]);
    $this->assertInstanceOf('\Pharborist\ConstantDeclarationStatementNode', $class_declaration->statements[0]);
    /** @var ClassMemberListNode $class_member_list */
    $class_member_list = $class_declaration->statements[1];
    $this->assertInstanceOf('\Pharborist\ClassMemberListNode', $class_member_list);
    $this->assertEquals('public', (string) $class_member_list->modifiers->visibility);
    $class_member = $class_member_list->members[0];
    $this->assertEquals('$publicProperty', (string) $class_member->name);
    $this->assertEquals('1', (string) $class_member->initialValue);
    //@todo test other properties
    /** @var ClassMethodNode $method */
    $method = $class_declaration->statements[6];
    $this->assertInstanceOf('\Pharborist\ClassMethodNode', $method);
    $this->assertEquals('myMethod', (string) $method->name);
    //@todo test other methods
    //@todo test trait stuff
  }

  /**
   * Test interface declaration.
   * @covers Pharborist\Parser
   */
  public function testInterfaceDeclaration() {
    $snippet = <<<'EOF'
interface MyInterface extends SomeInterface, AnotherInterface {
  const MY_CONST = 1;
  public function myMethod();
}
EOF;
    /** @var InterfaceNode $interface_declaration */
    $interface_declaration = $this->parseSnippet($snippet, '\Pharborist\InterfaceNode');
    $this->assertEquals('MyInterface', (string) $interface_declaration->name);
    $this->assertEquals('SomeInterface', (string) $interface_declaration->extends[0]);
    $this->assertEquals('AnotherInterface', (string) $interface_declaration->extends[1]);
    /** @var InterfaceMethodNode $method */
    $method = $interface_declaration->statements[1];
    $this->assertEquals('myMethod', (string) $method->name);
    $this->assertEquals('public', (string) $method->visibility);
    //@todo test interface constant
  }

  /**
   * Test trait declaration.
   * @covers Pharborist\Parser
   */
  public function testTraitDeclaration() {
    $snippet = <<<'EOF'
trait MyTrait extends ParentClass implements SomeInterface, AnotherInterface {
  // trait statements are covered by testClassDeclaration
  const MY_CONST = 1;
}
EOF;
    /** @var TraitNode $trait_declaration */
    $trait_declaration = $this->parseSnippet($snippet, '\Pharborist\TraitNode');
    $this->assertEquals('MyTrait', (string) $trait_declaration->name);
    $this->assertEquals('ParentClass', (string) $trait_declaration->extends);
    $this->assertEquals('SomeInterface', (string) $trait_declaration->implements[0]);
    $this->assertEquals('AnotherInterface', (string) $trait_declaration->implements[1]);
  }

  /**
   * Test if control structure.
   * @covers Pharborist\Parser
   */
  public function testIf() {
    $snippet = <<<'EOF'
if ($condition) {
}
elseif ($other_condition) {
}
elseif ($another_condition) {
}
else {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\IfNode');
    //@todo
  }

  /**
   * Test alternative if control structure.
   * @covers Pharborist\Parser
   */
  public function testAlternativeIf() {
    $snippet = <<<'EOF'
if ($condition):

elseif ($other_condition):

elseif ($another_condition):
  ;
else:

endif;
EOF;
    $this->parseSnippet($snippet, '\Pharborist\IfNode');
    //@todo
  }

  /**
   * Test foreach control structure.
   * @covers Pharborist\Parser
   */
  public function testForeach() {
    $snippet = <<<'EOF'
foreach ($array as $k => &$v) {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    //@todo
  }

  /**
   * Test alternative foreach control structure.
   * @covers Pharborist\Parser
   */
  public function testAlternativeForeach() {
    $snippet = <<<'EOF'
foreach ($array as $k => &$v):

endforeach;
EOF;
    $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    //@todo
  }

  /**
   * Test while control structure.
   * @covers Pharborist\Parser
   */
  public function testWhile() {
    $snippet = <<<'EOF'
while ($cond) {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    //@todo
  }

  /**
   * Test while control structure.
   * @covers Pharborist\Parser
   */
  public function testAlternativeWhile() {
    $snippet = <<<'EOF'
while ($cond):

endwhile;
EOF;
    $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    //@todo
  }

  /**
   * Test do..while control structure.
   * @covers Pharborist\Parser
   */
  public function testDoWhile() {
    $snippet = <<<'EOF'
do {
} while ($cond);
EOF;
    $this->parseSnippet($snippet, '\Pharborist\DoWhileNode');
    //@todo
  }

  /**
   * Test for control structure.
   * @covers Pharborist\Parser
   */
  public function testFor() {
    $snippet = <<<'EOF'
for ($i = 0; $i < 10; ++$i) {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\ForNode');
    //@todo
  }

  /**
   * Test for control structure.
   * @covers Pharborist\Parser
   */
  public function testAlternativeFor() {
    $snippet = <<<'EOF'
for ($i = 0; $i < 10; ++$i):

endfor;
EOF;
    $this->parseSnippet($snippet, '\Pharborist\ForNode');
    //@todo
  }

  /**
   * Test for(;;).
   */
  public function testForever() {
    $snippet = <<<'EOF'
for (;;) {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\ForNode');
    //@todo
  }

  /**
   * Test switch control structure.
   * @covers Pharborist\Parser
   */
  public function testSwitch() {
    $snippet = <<<'EOF'
switch ($cond) {
  case 'a':
    break;
  case 'fall':
  case 'through':
    break;
  default:
    break;
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\SwitchNode');
    //@todo
  }

  /**
   * Test switch control structure.
   * @covers Pharborist\Parser
   */
  public function testAlternativeSwitch() {
    $snippet = <<<'EOF'
switch ($cond):
  case 'a':
    break;
  case 'fall':
  case 'through':
    break;
  default:
    break;
endswitch;
EOF;
    $this->parseSnippet($snippet, '\Pharborist\SwitchNode');
    //@todo
  }

  /**
   * Test try/catch control structure.
   * @covers Pharborist\Parser
   */
  public function testTryCatch() {
    $snippet = <<<'EOF'
try {
}
catch (SomeException $e) {
}
catch (OtherException $e) {
}
EOF;
    $this->parseSnippet($snippet, '\Pharborist\TryCatchNode');
    //@todo
  }

  /**
   * Helper function to parse an expression.
   * @param string $expression
   * @param string $expected_type
   * @return Node
   */
  public function parseExpression($expression, $expected_type) {
    $statement_snippet = $expression . ';';
    /** @var ExpressionStatementNode $statement_node */
    $statement_node = $this->parseSnippet($statement_snippet, '\Pharborist\ExpressionStatementNode');
    $expression_node = $statement_node->children[0];
    $this->assertInstanceOf($expected_type, $expression_node);
    return $expression_node;
  }

  /**
   * Test array.
   */
  public function testArray() {
    //@todo
    $this->parseExpression('array(3, 5, 8, )', '\Pharborist\ArrayNode');
    $this->parseExpression('[3, 5, 8]', '\Pharborist\ArrayNode');
    $this->parseExpression('array("a" => 1, "b" => 2)', '\Pharborist\ArrayNode');
    $this->parseExpression('["a" => 1, "b" => 2]', '\Pharborist\ArrayNode');
    $this->parseExpression('[&$a, "k" => &$v]', '\Pharborist\ArrayNode');
    $this->parseSnippet('const MY_ARRAY = array(3, 5, 8, );', '\Pharborist\ConstantDeclarationStatementNode');
  }

  /**
   * Helper function to parse a variable.
   * @param string $variable
   * @param string $expected_type
   * @return Node
   */
  public function parseVariable($variable, $expected_type) {
    $statement_snippet = 'unset(' . $variable . ');';
    /** @var UnsetStatementNode $statement_node */
    $statement_node = $this->parseSnippet($statement_snippet, '\Pharborist\UnsetStatementNode');
    $unset_node = $statement_node->functionCall;
    $variable_node = $unset_node->arguments[0];
    $this->assertInstanceOf($expected_type, $variable_node);
    return $variable_node;
  }

  /**
   * Test variable.
   */
  public function testVariable() {
    //@todo
    $this->parseVariable('$a', '\Pharborist\VariableNode');
    $this->parseVariable('${$a}', '\Pharborist\CompoundVariableNode');
    $this->parseVariable('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->parseVariable('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->parseVariable('$$a', '\Pharborist\VariableVariableNode');
    $this->parseVariable('MyClass::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseVariable('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->parseVariable('static::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseVariable('$c::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseVariable('$c[0]::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseVariable('$c{0}::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseVariable('$o->property', '\Pharborist\ObjectPropertyNode');
    $this->parseVariable('$o->{$a}', '\Pharborist\ObjectPropertyNode');
    $this->parseVariable('$o->$a', '\Pharborist\ObjectPropertyNode');
    $this->parseVariable('$o->$$a', '\Pharborist\ObjectPropertyNode');
    $this->parseVariable('$a()', '\Pharborist\FunctionCallNode');
    $this->parseVariable('$o->$a()', '\Pharborist\ObjectMethodCallNode');
    $this->parseVariable('a()', '\Pharborist\FunctionCallNode');
    $this->parseVariable('namespace\MyClass::a()', '\Pharborist\ClassMethodCallNode');
    $this->parseVariable('MyNamespace\MyClass::$a()', '\Pharborist\ClassMethodCallNode');
    $this->parseVariable('MyClass::{$a}()', '\Pharborist\ClassMethodCallNode');
    $this->parseVariable('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->parseVariable('$class::${$f}()', '\Pharborist\ClassMethodCallNode');
    $this->parseVariable('$class::${$f}[0]', '\Pharborist\ArrayLookupNode');
  }

  /**
   * Test expression.
   */
  public function testExpression() {
    //@todo
    $this->parseExpression('$a', '\Pharborist\VariableNode');
    $this->parseExpression('${$a}', '\Pharborist\CompoundVariableNode');
    $this->parseExpression('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->parseExpression('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->parseExpression('$$a', '\Pharborist\VariableVariableNode');
    $this->parseExpression('MyClass::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseExpression('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->parseExpression('static::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseExpression('$c::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseExpression('$c[0]::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseExpression('$c{0}::$a', '\Pharborist\ClassMemberLookupNode');
    $this->parseExpression('$o->property', '\Pharborist\ObjectPropertyNode');
    $this->parseExpression('$o->{$a}', '\Pharborist\ObjectPropertyNode');
    $this->parseExpression('$o->$a', '\Pharborist\ObjectPropertyNode');
    $this->parseExpression('$o->$$a', '\Pharborist\ObjectPropertyNode');
    $this->parseExpression('$a()', '\Pharborist\FunctionCallNode');
    $this->parseExpression('$o->$a()', '\Pharborist\ObjectMethodCallNode');
    $this->parseExpression('a()', '\Pharborist\FunctionCallNode');
    $this->parseExpression('namespace\MyClass::a()', '\Pharborist\ClassMethodCallNode');
    $this->parseExpression('MyNamespace\MyClass::$a()', '\Pharborist\ClassMethodCallNode');
    $this->parseExpression('MyNamespace\MyClass::$a[0]()', '\Pharborist\ClassMethodCallNode');
    $this->parseExpression('MyClass::{$a}()', '\Pharborist\ClassMethodCallNode');
    $this->parseExpression('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->parseExpression('$a = $b++', '\Pharborist\AssignNode');
    $this->parseExpression('$a = $b ?: $c', '\Pharborist\AssignNode');
    /** @var TernaryOperationNode $ternary_node */
    $ternary_node = $this->parseExpression('$a ? $b : $c ? $d : $e', '\Pharborist\TernaryOperationNode');
    $this->assertEquals('$a ? $b : $c', $ternary_node->condition);
    $this->assertEquals('$d', $ternary_node->then);
    $this->assertEquals('$e', $ternary_node->else);

    $this->parseExpression('$a or $b', '\Pharborist\LogicalOrNode');
    $this->parseExpression('$a xor $b', '\Pharborist\LogicalXorNode');
    $this->parseExpression('$a and $b', '\Pharborist\LogicalAndNode');
    $this->parseExpression('$a = $b', '\Pharborist\AssignNode');
    $this->parseExpression('$a += $b', '\Pharborist\AddAssignNode');
    $this->parseExpression('$a .= $b', '\Pharborist\ConcatAssignNode');
    $this->parseExpression('$a /= $b', '\Pharborist\DivideAssignNode');
    $this->parseExpression('$a -= $b', '\Pharborist\SubtractAssignNode');
    $this->parseExpression('$a %= $b', '\Pharborist\ModulusAssignNode');
    $this->parseExpression('$a *= $b', '\Pharborist\MultiplyAssignNode');
    $this->parseExpression('$a &= $b', '\Pharborist\BitwiseAndAssignNode');
    $this->parseExpression('$a <<= $b', '\Pharborist\BitwiseShiftLeftAssignNode');
    $this->parseExpression('$a >>= $b', '\Pharborist\BitwiseShiftRightAssignNode');
    $this->parseExpression('$a ^= $b', '\Pharborist\BitwiseXorAssignNode');
    $this->parseExpression('$a || $b', '\Pharborist\BooleanOrNode');
    $this->parseExpression('$a && $b', '\Pharborist\BooleanAndNode');
    $this->parseExpression('$a | $b', '\Pharborist\BitwiseOrNode');
    $this->parseExpression('$a & $b', '\Pharborist\BitwiseAndNode');
    $this->parseExpression('$a ^ $b', '\Pharborist\BitwiseXorNode');
    $this->parseExpression('$a == $b', '\Pharborist\EqualNode');
    $this->parseExpression('$a === $b', '\Pharborist\IdenticalNode');
    $this->parseExpression('$a != $b', '\Pharborist\NotEqualNode');
    $this->parseExpression('$a !== $b', '\Pharborist\NotIdenticalNode');
    $this->parseExpression('$a < $b', '\Pharborist\LessThanNode');
    $this->parseExpression('$a <= $b', '\Pharborist\LessThanOrEqualToNode');
    $this->parseExpression('$a >= $b', '\Pharborist\GreaterThanOrEqualToNode');
    $this->parseExpression('$a > $b', '\Pharborist\GreaterThanNode');
    $this->parseExpression('$a << $b', '\Pharborist\BitwiseShiftLeftNode');
    $this->parseExpression('$a >> $b', '\Pharborist\BitwiseShiftRightNode');
    $this->parseExpression('$a + $b', '\Pharborist\AddNode');
    $this->parseExpression('$a - $b', '\Pharborist\SubtractNode');
    $this->parseExpression('$a / $b', '\Pharborist\DivideNode');
    $this->parseExpression('$a * $b', '\Pharborist\MultiplyNode');
    $this->parseExpression('$a % $b', '\Pharborist\ModulusNode');
    $this->parseExpression('!$a', '\Pharborist\BooleanNotNode');
    $this->parseExpression('$a instanceof $b', '\Pharborist\InstanceOfNode');
    $this->parseExpression('@func()', '\Pharborist\SuppressWarningNode');
    $this->parseExpression('~$a', '\Pharborist\BitwiseNotNode');
    $this->parseExpression('clone $a', '\Pharborist\CloneNode');
    $this->parseExpression('print $a', '\Pharborist\PrintNode');
    $this->parseExpression('(array) $a', '\Pharborist\ArrayCastNode');
    $this->parseExpression('(object) $a', '\Pharborist\ObjectCastNode');
    $this->parseExpression('(bool) $a', '\Pharborist\BooleanCastNode');
    $this->parseExpression('(int) $a', '\Pharborist\IntegerCastNode');
    $this->parseExpression('(float) $a', '\Pharborist\FloatCastNode');
    $this->parseExpression('(unset) $a', '\Pharborist\UnsetCastNode');
    $this->parseExpression('(string) $a', '\Pharborist\StringCastNode');
    $this->parseExpression('--$a', '\Pharborist\PreDecrementNode');
    $this->parseExpression('++$a', '\Pharborist\PreIncrementNode');
    $this->parseExpression('$a--', '\Pharborist\PostDecrementNode');
    $this->parseExpression('$a++', '\Pharborist\PostIncrementNode');
    $this->parseExpression('+$a', '\Pharborist\PlusNode');
    $this->parseExpression('-$a', '\Pharborist\NegateNode');
  }

  /**
   * Test invalid comparison expression.
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage Non-associative operators of equal precedence can not be next to each other!
   */
  public function testInvalidComparison() {
    $this->parseExpression('1 <= 1 == 2 >= 2 == 2', '\Pharborist\EqualNode');
  }

  /**
   * Test operator precedence.
   */
  public function testPrecedence() {
    $this->parseExpression('4 + 2 * 3', '\Pharborist\AddNode');
  }

  /**
   * Test valid comparison expression of different precedence.
   */
  public function testComparison() {
    $this->parseExpression('1 <= 1 == 1', '\Pharborist\EqualNode');
  }

  /**
   * Test static expression.
   */
  public function testStaticExpression() {
    //@todo
    $this->parseSnippet('const MY_CONST = namespace\MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
    $this->parseSnippet('const MY_CONST = MyNamespace\MyClass::MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
    $this->parseSnippet('const MY_CONST = MyNamespace\MyClass::class;', '\Pharborist\ConstantDeclarationStatementNode');
    $this->parseSnippet('const MY_CONST = static::MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
  }

  /**
   * Test dynamic class name.
   */
  public function testDynamicClassName() {
    //@todo
    $this->parseExpression('new $a::$b->c', '\Pharborist\NewNode');
  }

  /**
   * Test function call.
   * @covers Pharborist\Parser
   */
  public function testFunctionCall() {
    //@todo
    $this->parseExpression('do_something(&$a, $b)', '\Pharborist\FunctionCallNode');
  }

  /**
   * Test static variable list.
   * @covers Pharborist\Parser
   */
  public function testStaticVariableList() {
    //@todo
    $this->parseSnippet('static $a, $b = 1;', '\Pharborist\StaticVariableStatementNode');
  }

  /**
   * Test (new expr) expression.
   */
  public function testParenNewExpression() {
    //@todo
    $this->parseExpression('(new $class($a, $b))->$method()', '\Pharborist\ObjectMethodCallNode');
  }

  /**
   * Test anonymous function.
   */
  public function testAnonymousFunction() {
    //@todo
    $this->parseSnippet('function(){};', '\Pharborist\ExpressionStatementNode');
    $this->parseSnippet('function($a, $b) use ($x, $y) { };', '\Pharborist\ExpressionStatementNode');
    $this->parseSnippet('$f = function($a, $b) use ($x, $y) { };', '\Pharborist\ExpressionStatementNode');
  }
  
}
