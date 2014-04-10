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
    $first_child = $tree->getFirst();
    $this->assertInstanceOf($expected_type, $first_child);
    return $first_child;
  }

  /**
   * Test parsing empty source file.
   */
  public function testParseEmpty() {
    $tree = Parser::parseSource('');
    $this->assertEquals(0, $tree->getChildCount());
  }

  /**
   * Test parsing php file with no code.
   */
  public function testParseBlank() {
    $tree = Parser::parseSource("<?php\n");
    $this->assertEquals(1, $tree->getChildCount());
    $this->assertInstanceOf('\Pharborist\TokenNode', $tree->getFirst());
    /** @var TokenNode $child */
    $child = $tree->getFirst();
    $this->assertEquals(T_OPEN_TAG, $child->getType());
  }

  /**
   * Test parsing namespace.
   */
  public function testNamespace() {
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace MyNamespace\Test ;', '\Pharborist\NamespaceNode');
    $this->assertEquals('MyNamespace\Test', (string) $namespace_node->getName());

    // Test with body
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace MyNamespace\Test\Body { }', '\Pharborist\NamespaceNode');
    $this->assertEquals('MyNamespace\Test\Body', (string) $namespace_node->getName());
    $this->assertNotNull($namespace_node->getBody());

    // Test global
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace { }', '\Pharborist\NamespaceNode');
    $this->assertNull($namespace_node->getName());
    $this->assertNotNull($namespace_node->getBody());
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
    $use_declaration = $use_declaration_statement->getDeclarations()[0];
    $this->assertEquals('MyNamespace\MyClass', (string) $use_declaration->getNamespacePath());
    $this->assertEquals('MyAlias', (string) $use_declaration->getAlias());
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
    $this->assertEquals('my_func', (string) $function_declaration->getName());
    $parameters = $function_declaration->getParameters();
    $parameter = $parameters[0];
    $this->assertEquals('$a', (string) $parameter->getName());
    $this->assertEquals('array', (string) $parameter->getClassType());
    $parameter = $parameters[1];
    $this->assertEquals('$b', (string) $parameter->getName());
    $this->assertEquals('callable', (string) $parameter->getClassType());
    $parameter = $parameters[2];
    $this->assertEquals('$c', (string) $parameter->getName());
    $this->assertEquals('namespace\Test', (string) $parameter->getClassType());
    $parameter = $parameters[3];
    $this->assertEquals('$d', (string) $parameter->getName());
    $this->assertEquals('\MyNamespace\Test', (string) $parameter->getClassType());
    $parameter = $parameters[4];
    $this->assertEquals('$e', (string) $parameter->getName());
    $this->assertEquals('1', (string) $parameter->getDefaultValue());
  }

  /**
   * Test parsing const declaration.
   */
  public function testConstDeclaration() {
    /** @var ConstantDeclarationStatementNode $const_declaration_list */
    $const_declaration_list = $this->parseSnippet('const MyConst = 1;', '\Pharborist\ConstantDeclarationStatementNode');
    $const_declaration = $const_declaration_list->getDeclarations()[0];
    $this->assertEquals('MyConst', (string) $const_declaration->getName());
    $this->assertEquals('1', (string) $const_declaration->getValue());
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
    $this->assertEquals('MyClass', (string) $class_declaration->getName());
    $this->assertEquals('ParentClass', (string) $class_declaration->getExtends());
    $this->assertEquals('SomeInterface', (string) $class_declaration->getImplements()[0]);
    $this->assertEquals('AnotherInterface', (string) $class_declaration->getImplements()[1]);
    $this->assertInstanceOf('\Pharborist\ConstantDeclarationStatementNode', $class_declaration->getStatements()[0]);

    /** @var ClassMemberListNode $class_member_list */
    $statements = $class_declaration->getStatements();
    $class_member_list = $statements[1];
    $this->assertInstanceOf('\Pharborist\ClassMemberListNode', $class_member_list);
    $this->assertEquals('public', (string) $class_member_list->getModifiers()->getVisibility());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$publicProperty', (string) $class_member->getName());
    $this->assertEquals('1', (string) $class_member->getInitialValue());

    $class_member_list = $statements[2];
    $this->assertEquals('protected', (string) $class_member_list->getModifiers()->getVisibility());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$protectedProperty', (string) $class_member->getName());

    $class_member_list = $statements[3];
    $this->assertEquals('private', (string) $class_member_list->getModifiers()->getVisibility());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$privateProperty', (string) $class_member->getName());

    $class_member_list = $statements[4];
    $this->assertEquals('public', (string) $class_member_list->getModifiers()->getVisibility());
    $this->assertEquals('static', (string) $class_member_list->getModifiers()->getStatic());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$classProperty', (string) $class_member->getName());

    /** @var ClassMethodNode $method */
    $method = $statements[6];
    $this->assertInstanceOf('\Pharborist\ClassMethodNode', $method);
    $this->assertEquals('myMethod', (string) $method->getName());
    $this->assertEquals('public', (string) $method->getModifiers()->getVisibility());

    $method = $statements[7];
    $this->assertEquals('noOverride', (string) $method->getName());
    $this->assertEquals('public', (string) $method->getModifiers()->getVisibility());
    $this->assertEquals('final', (string) $method->getModifiers()->getFinal());

    $method = $statements[8];
    $this->assertEquals('classMethod', (string) $method->getName());
    $this->assertEquals('public', (string) $method->getModifiers()->getVisibility());
    $this->assertEquals('static', (string) $method->getModifiers()->getStatic());

    $method = $statements[9];
    $this->assertEquals('mustImplement', (string) $method->getName());
    $this->assertEquals('public', (string) $method->getModifiers()->getVisibility());
    $this->assertEquals('abstract', (string) $method->getModifiers()->getAbstract());

    /** @var TraitUseNode $trait_use */
    $trait_use = $statements[10];
    $traits = $trait_use->getTraits();
    $this->assertInstanceOf('\Pharborist\TraitUseNode', $trait_use);
    $this->assertEquals('A', (string) $traits[0]);
    $this->assertEquals('B', (string) $traits[1]);
    $this->assertEquals('C', (string) $traits[2]);

    $adaptations = $trait_use->getAdaptations();
    /** @var TraitPrecedenceNode $trait_precedence */
    $trait_precedence = $adaptations[0];
    $this->assertInstanceOf('\Pharborist\TraitPrecedenceNode', $trait_precedence);
    $this->assertInstanceOf('\Pharborist\TraitMethodReferenceNode', $trait_precedence->getTraitMethodReference());
    $this->assertEquals('B::smallTalk', (string) $trait_precedence->getTraitMethodReference());
    $this->assertEquals('B', (string) $trait_precedence->getTraitMethodReference()->getTraitName());
    $this->assertEquals('smallTalk', (string) $trait_precedence->getTraitMethodReference()->getMethodReference());
    $this->assertEquals('A', (string) $trait_precedence->getTraitNames()[0]);

    $trait_precedence = $adaptations[1];
    $this->assertInstanceOf('\Pharborist\TraitPrecedenceNode', $trait_precedence);
    $this->assertInstanceOf('\Pharborist\TraitMethodReferenceNode', $trait_precedence->getTraitMethodReference());
    $this->assertEquals('A::bigTalk', (string) $trait_precedence->getTraitMethodReference());
    $this->assertEquals('A', (string) $trait_precedence->getTraitMethodReference()->getTraitName());
    $this->assertEquals('bigTalk', (string) $trait_precedence->getTraitMethodReference()->getMethodReference());
    $this->assertEquals('B', (string) $trait_precedence->getTraitNames()[0]);
    $this->assertEquals('C', (string) $trait_precedence->getTraitNames()[1]);

    /** @var TraitAliasNode $trait_alias */
    $trait_alias = $adaptations[2];
    $this->assertInstanceOf('\Pharborist\TraitAliasNode', $trait_alias);
    $this->assertInstanceOf('\Pharborist\TraitMethodReferenceNode', $trait_alias->getTraitMethodReference());
    $this->assertEquals('B::bigTalk', (string) $trait_alias->getTraitMethodReference());
    $this->assertEquals('B', (string) $trait_alias->getTraitMethodReference()->getTraitName());
    $this->assertEquals('bigTalk', (string) $trait_alias->getTraitMethodReference()->getMethodReference());
    $this->assertEquals('talk', (string) $trait_alias->getAlias());
  }

  /**
   * Test interface declaration.
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
    $this->assertEquals('MyInterface', (string) $interface_declaration->getName());
    $this->assertEquals('SomeInterface', (string) $interface_declaration->getExtends()[0]);
    $this->assertEquals('AnotherInterface', (string) $interface_declaration->getExtends()[1]);

    /** @var ConstantDeclarationStatementNode $constant_declaration_statement */
    $constant_declaration_statement = $interface_declaration->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\ConstantDeclarationStatementNode', $constant_declaration_statement);
    $constant_declaration = $constant_declaration_statement->getDeclarations()[0];
    $this->assertEquals('MY_CONST', (string) $constant_declaration->getName());
    $this->assertEquals('1', (string) $constant_declaration->getValue());

    /** @var InterfaceMethodNode $method */
    $method = $interface_declaration->getStatements()[1];
    $this->assertEquals('myMethod', (string) $method->getName());
    $this->assertEquals('public', (string) $method->getVisibility());
  }

  /**
   * Test trait declaration.
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
    $this->assertEquals('MyTrait', (string) $trait_declaration->getName());
    $this->assertEquals('ParentClass', (string) $trait_declaration->getExtends());
    $implements = $trait_declaration->getImplements();
    $this->assertEquals('SomeInterface', (string) $implements[0]);
    $this->assertEquals('AnotherInterface', (string) $implements[1]);
  }

  /**
   * Test if control structure.
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
    /** @var IfNode $if */
    $if = $this->parseSnippet($snippet, '\Pharborist\IfNode');
    $this->assertEquals('($condition)', (string) $if->getCondition());
    $this->assertEquals(2, count($if->getElseIfList()));
    $this->assertEquals('($other_condition)', (string) $if->getElseIfList()[0]->getCondition());
    $this->assertEquals('($another_condition)', (string) $if->getElseIfList()[1]->getCondition());
    $this->assertNotNull($if->getElse());
  }

  /**
   * Test alternative if control structure.
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
    /** @var IfNode $if */
    $if = $this->parseSnippet($snippet, '\Pharborist\IfNode');
    $this->assertEquals('($condition)', (string) $if->getCondition());
    $this->assertEquals(2, count($if->getElseIfList()));
    $this->assertEquals('($other_condition)', (string) $if->getElseIfList()[0]->getCondition());
    $this->assertEquals('($another_condition)', (string) $if->getElseIfList()[1]->getCondition());
    $this->assertNotNull($if->getElse());
  }

  /**
   * Test foreach control structure.
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
    $expression_node = $statement_node->getFirst();
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
    $unset_node = $statement_node->getFunctionCall();
    $variable_node = $unset_node->getArguments()[0];
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
    $this->parseVariable('$a()', '\Pharborist\CallbackCallNode');
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
    $this->assertEquals('$a ? $b : $c', (string) $ternary_node->getCondition());
    $this->assertEquals('$d', (string) $ternary_node->getThen());
    $this->assertEquals('$e', (string) $ternary_node->getElse());

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
   */
  public function testFunctionCall() {
    //@todo
    $this->parseExpression('do_something(&$a, $b)', '\Pharborist\FunctionCallNode');
  }

  /**
   * Test static variable list.
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

  public function testTokenIteration() {
    /** @var \Pharborist\ExpressionStatementNode $tree */
    $tree = $this->parseSnippet('1 + 2;', '\Pharborist\ExpressionStatementNode');
    $one = $tree->getFirstToken();
    $this->assertNull($one->previousToken());
    $this->assertEquals('1', $one->getText());
    $op = $one->nextToken()->nextToken();
    $this->assertEquals('+', $op->getText());
    $two = $op->nextToken()->nextToken();
    $this->assertEquals('2', $two->getText());
    $semicolon = $two->nextToken();
    $this->assertEquals(';', $semicolon->getText());
    $this->assertNull($semicolon->nextToken());
    $this->assertEquals('2', $semicolon->previousToken()->getText());
  }
}
