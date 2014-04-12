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
   * Helper function to parse a snippet block.
   */
  public function parseSnippetBlock($snippet) {
    $tree = Parser::parseSnippet($snippet);
    $source = (string) $tree;
    $this->assertEquals($snippet, $source);
    return $tree;
  }

  /**
   * Helper function to parse a snippet.
   */
  public function parseSnippet($snippet, $expected_type) {
    $tree = $this->parseSnippetBlock($snippet);
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
/** Class doc comment. */
abstract class MyClass extends ParentClass implements SomeInterface, AnotherInterface {
  /** const doc comment */
  const MY_CONST = 1;
  public $publicProperty = 1;
  protected $protectedProperty;
  private $privateProperty;
  static public $classProperty;
  var $backwardsCompatibility;

  /** method doc comment. */
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
    $this->assertEquals('/** Class doc comment. */', $class_declaration->getDocComment());
    $this->assertEquals('MyClass', (string) $class_declaration->getName());
    $this->assertEquals('ParentClass', (string) $class_declaration->getExtends());
    $this->assertEquals('SomeInterface', (string) $class_declaration->getImplements()[0]);
    $this->assertEquals('AnotherInterface', (string) $class_declaration->getImplements()[1]);
    $statements = $class_declaration->getStatements();

    /** @var ConstantDeclarationStatementNode $const_statement */
    $const_statement = $statements[0];
    $this->assertInstanceOf('\Pharborist\ConstantDeclarationStatementNode', $const_statement);
    $this->assertEquals('/** const doc comment */', $const_statement->getDocComment());

    /** @var ClassMemberListNode $class_member_list */
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
    $this->assertEquals('/** method doc comment. */', $method->getDocComment());
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
if ($condition) { then(); }
elseif ($other_condition) { other_then(); }
elseif ($another_condition) {
}
else { do_else(); }
EOF;
    /** @var IfNode $if */
    $if = $this->parseSnippet($snippet, '\Pharborist\IfNode');
    $this->assertEquals('($condition)', (string) $if->getCondition());
    $this->assertEquals('{ then(); }', (string) $if->getThen());
    $else_ifs = $if->getElseIfList();
    $this->assertEquals(2, count($else_ifs));
    $this->assertEquals('($other_condition)', (string) $else_ifs[0]->getCondition());
    $this->assertEquals('{ other_then(); }', (string) $else_ifs[0]->getThen());
    $this->assertEquals('($another_condition)', (string) $else_ifs[1]->getCondition());
    $this->assertEquals('{ do_else(); }', (string) $if->getElse());
  }

  /**
   * Test alternative if control structure.
   */
  public function testAlternativeIf() {
    $snippet = <<<'EOF'
if ($condition):
  then();
elseif ($other_condition):
  other_then();
elseif ($another_condition):
  ;
else:
  do_else();
endif;
EOF;
    /** @var IfNode $if */
    $if = $this->parseSnippet($snippet, '\Pharborist\IfNode');
    $this->assertEquals('($condition)', (string) $if->getCondition());
    $this->assertEquals('then();', (string) $if->getThen());
    $else_ifs = $if->getElseIfList();
    $this->assertEquals(2, count($else_ifs));
    $this->assertEquals('($other_condition)', (string) $else_ifs[0]->getCondition());
    $this->assertEquals('other_then();', (string) $else_ifs[0]->getThen());
    $this->assertEquals('($another_condition)', (string) $else_ifs[1]->getCondition());
    $this->assertEquals('do_else();', (string) $if->getElse());
  }

  /**
   * Test foreach control structure.
   */
  public function testForeach() {
    $snippet = <<<'EOF'
foreach ($array as $k => &$v)
  body();
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    $this->assertEquals('$array', (string) $foreach->getOnEach());
    $this->assertEquals('$k', (string) $foreach->getKey());
    $this->assertEquals('&$v', (string) $foreach->getValue());
    $this->assertEquals('body();', (string) $foreach->getBody());

    $snippet = <<<'EOF'
foreach ($array as $v)
  body();
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    $this->assertEquals('$array', (string) $foreach->getOnEach());
    $this->assertNull($foreach->getKey());
    $this->assertEquals('$v', (string) $foreach->getValue());
    $this->assertEquals('body();', (string) $foreach->getBody());
  }

  /**
   * Test alternative foreach control structure.
   */
  public function testAlternativeForeach() {
    $snippet = <<<'EOF'
foreach ($array as $k => &$v):
  body();
endforeach;
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    $this->assertEquals('$array', (string) $foreach->getOnEach());
    $this->assertEquals('$k', (string) $foreach->getKey());
    $this->assertEquals('&$v', (string) $foreach->getValue());
    $this->assertEquals('body();', (string) $foreach->getBody());

    $snippet = <<<'EOF'
foreach ($array as $v):
  body();
endforeach;
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ForeachNode');
    $this->assertEquals('$array', (string) $foreach->getOnEach());
    $this->assertNull($foreach->getKey());
    $this->assertEquals('$v', (string) $foreach->getValue());
    $this->assertEquals('body();', (string) $foreach->getBody());
  }

  /**
   * Test while control structure.
   */
  public function testWhile() {
    $snippet = <<<'EOF'
while ($cond)
  body();
EOF;
    /** @var WhileNode $while */
    $while = $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    $this->assertEquals('($cond)', (string) $while->getCondition());
    $this->assertEquals('body();', (string) $while->getBody());
  }

  /**
   * Test while control structure.
   */
  public function testAlternativeWhile() {
    $snippet = <<<'EOF'
while ($cond):
  body();
endwhile;
EOF;
    /** @var WhileNode $while */
    $while = $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    $this->assertEquals('($cond)', (string) $while->getCondition());
    $this->assertEquals('body();', (string) $while->getBody());
  }

  /**
   * Test do..while control structure.
   */
  public function testDoWhile() {
    $snippet = <<<'EOF'
do
  body();
while ($cond);
EOF;
    /** @var DoWhileNode $do_while */
    $do_while = $this->parseSnippet($snippet, '\Pharborist\DoWhileNode');
    $this->assertEquals('body();', (string) $do_while->getBody());
    $this->assertEquals('($cond)', (string) $do_while->getCondition());
  }

  /**
   * Test for control structure.
   */
  public function testFor() {
    $snippet = <<<'EOF'
for ($i = 0; $i < 10; ++$i)
  body();
EOF;
    /** @var ForNode $for */
    $for = $this->parseSnippet($snippet, '\Pharborist\ForNode');
    $this->assertEquals('$i = 0', (string) $for->getInitial());
    $this->assertEquals('$i < 10', (string) $for->getCondition());
    $this->assertEquals('++$i', (string) $for->getStep());
    $this->assertEquals('body();', (string) $for->getBody());
  }

  /**
   * Test for control structure.
   */
  public function testAlternativeFor() {
    $snippet = <<<'EOF'
for ($i = 0; $i < 10; ++$i):
  body();
endfor;
EOF;
    /** @var ForNode $for */
    $for = $this->parseSnippet($snippet, '\Pharborist\ForNode');
    $this->assertEquals('$i = 0', (string) $for->getInitial());
    $this->assertEquals('$i < 10', (string) $for->getCondition());
    $this->assertEquals('++$i', (string) $for->getStep());
    $this->assertEquals('body();', (string) $for->getBody());
  }

  /**
   * Test for(;;).
   */
  public function testForever() {
    $snippet = <<<'EOF'
for (;;)
  body();
EOF;
    /** @var ForNode $for */
    $for = $this->parseSnippet($snippet, '\Pharborist\ForNode');
    $this->assertEquals('', (string) $for->getInitial());
    $this->assertEquals('', (string) $for->getCondition());
    $this->assertEquals('', (string) $for->getStep());
    $this->assertEquals('body();', (string) $for->getBody());
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
    /** @var SwitchNode $switch */
    $switch = $this->parseSnippet($snippet, '\Pharborist\SwitchNode');
    $this->assertEquals('($cond)', (string) $switch->getSwitchOn());
    $cases = $switch->getCases();
    $case = $cases[0];
    $this->assertEquals("'a'", (string) $case->getMatchOn());
    $this->assertEquals('break;', (string) $case->getBody());
    $case = $cases[1];
    $this->assertEquals("'fall'", (string) $case->getMatchOn());
    $this->assertNull($case->getBody());
    $case = $cases[2];
    $this->assertEquals("'through'", (string) $case->getMatchOn());
    $this->assertEquals('break;', (string) $case->getBody());
    $case = $cases[3];
    $this->assertInstanceOf('\Pharborist\DefaultNode', $case);
    $this->assertEquals('break;', (string) $case->getBody());
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
    /** @var SwitchNode $switch */
    $switch = $this->parseSnippet($snippet, '\Pharborist\SwitchNode');
    $this->assertEquals('($cond)', (string) $switch->getSwitchOn());
    $cases = $switch->getCases();
    $case = $cases[0];
    $this->assertEquals("'a'", (string) $case->getMatchOn());
    $this->assertEquals('break;', (string) $case->getBody());
    $case = $cases[1];
    $this->assertEquals("'fall'", (string) $case->getMatchOn());
    $this->assertNull($case->getBody());
    $case = $cases[2];
    $this->assertEquals("'through'", (string) $case->getMatchOn());
    $this->assertEquals('break;', (string) $case->getBody());
    $case = $cases[3];
    $this->assertInstanceOf('\Pharborist\DefaultNode', $case);
    $this->assertEquals('break;', (string) $case->getBody());
  }

  /**
   * Test try/catch control structure.
   */
  public function testTryCatch() {
    $snippet = <<<'EOF'
try { try_body(); }
catch (SomeException $e) { some_body(); }
catch (OtherException $e) { other_body(); }
EOF;
    /** @var TryCatchNode $try_catch */
    $try_catch = $this->parseSnippet($snippet, '\Pharborist\TryCatchNode');
    $this->assertEquals('{ try_body(); }', (string) $try_catch->getTry());
    $catches = $try_catch->getCatches();
    $catch = $catches[0];
    $this->assertEquals('SomeException', (string) $catch->getExceptionType());
    $this->assertEquals('$e', (string) $catch->getVariable());
    $this->assertEquals('{ some_body(); }', (string) $catch->getBody());
    $catch = $catches[1];
    $this->assertEquals('OtherException', (string) $catch->getExceptionType());
    $this->assertEquals('$e', (string) $catch->getVariable());
    $this->assertEquals('{ other_body(); }', (string) $catch->getBody());
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
    /** @var ArrayNode $array */
    $array = $this->parseExpression('array(3, 5, 8, )', '\Pharborist\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', (string) $elements[0]);
    $this->assertEquals('5', (string) $elements[1]);
    $this->assertEquals('8', (string) $elements[2]);

    $array = $this->parseExpression('[3, 5, 8]', '\Pharborist\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', (string) $elements[0]);
    $this->assertEquals('5', (string) $elements[1]);
    $this->assertEquals('8', (string) $elements[2]);

    $array = $this->parseExpression('array("a" => 1, "b" => 2)', '\Pharborist\ArrayNode');
    $elements = $array->getElements();
    /** @var ArrayPairNode $pair */
    $pair = $elements[0];
    $this->assertEquals('"a"', (string) $pair->getKey());
    $this->assertEquals('1', (string) $pair->getValue());
    $pair = $elements[1];
    $this->assertEquals('"b"', (string) $pair->getKey());
    $this->assertEquals('2', (string) $pair->getValue());

    $array = $this->parseExpression('["a" => 1, "b" => 2]', '\Pharborist\ArrayNode');
    $elements = $array->getElements();
    $pair = $elements[0];
    $this->assertEquals('"a"', (string) $pair->getKey());
    $this->assertEquals('1', (string) $pair->getValue());
    $pair = $elements[1];
    $this->assertEquals('"b"', (string) $pair->getKey());
    $this->assertEquals('2', (string) $pair->getValue());

    $array = $this->parseExpression('[&$a, "k" => &$v]', '\Pharborist\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('&$a', (string) $elements[0]);
    $pair = $elements[1];
    $this->assertEquals('"k"', (string) $pair->getKey());
    $this->assertEquals('&$v', (string) $pair->getValue());

    /** @var ConstantDeclarationStatementNode $constant_stmt */
    $constant_stmt = $this->parseSnippet('const MY_ARRAY = array(3, 5, 8, );', '\Pharborist\ConstantDeclarationStatementNode');
    $array = $constant_stmt->getDeclarations()[0]->getValue();
    $elements = $array->getElements();
    $this->assertEquals('3', (string) $elements[0]);
    $this->assertEquals('5', (string) $elements[1]);
    $this->assertEquals('8', (string) $elements[2]);
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
    $this->parseVariable('$a', '\Pharborist\VariableNode');

    /** @var CompoundVariableNode $compound_var */
    $compound_var = $this->parseVariable('${$a}', '\Pharborist\CompoundVariableNode');
    $this->assertEquals('$a', (string) $compound_var->getExpression());

    /** @var ArrayLookupNode $array_lookup */
    $array_lookup = $this->parseVariable('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    $array_lookup = $this->parseVariable('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    /** @var VariableVariableNode $var_var */
    $var_var = $this->parseVariable('$$a', '\Pharborist\VariableVariableNode');
    $this->assertEquals('$a', (string) $var_var->getVariable());

    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $this->parseVariable('MyClass::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('MyClass', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $array_lookup = $this->parseVariable('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('MyClass::$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('MyClass', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseVariable('static::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('static', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseVariable('$c::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseVariable('$c[0]::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c[0]', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    $class_member_lookup = $this->parseVariable('$c{0}::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c{0}', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    /** @var ObjectPropertyNode $obj_property_lookup */
    $obj_property_lookup = $this->parseVariable('$o->property', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('property', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseVariable('$o->{$a}', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('{$a}', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseVariable('$o->$a', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('$a', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseVariable('$o->$$a', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('$$a', (string) $obj_property_lookup->getProperty());
    $var_var = $obj_property_lookup->getProperty();
    $this->assertEquals('$a', (string) $var_var->getVariable());

    /** @var CallbackCallNode $callback_call */
    $callback_call = $this->parseVariable('$a()', '\Pharborist\CallbackCallNode');
    $this->assertEquals('$a', (string) $callback_call->getCallback());

    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseVariable('$o->$a()', '\Pharborist\ObjectMethodCallNode');
    $this->assertEquals('$o', (string) $obj_method_call->getObject());
    $this->assertEquals('$a', (string) $obj_method_call->getMethodName());

    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseVariable('a()', '\Pharborist\FunctionCallNode');
    $this->assertEquals('a', (string) $function_call->getNamespacePath());

    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $this->parseVariable('namespace\MyClass::a()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('namespace\MyClass', (string) $class_method_call->getClassName());
    $this->assertEquals('a', (string) $class_method_call->getMethodName());

    $class_method_call = $this->parseVariable('MyNamespace\MyClass::$a()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('MyNamespace\MyClass', $class_method_call->getClassName());
    $this->assertEquals('$a', (string) $class_method_call->getMethodName());

    $class_method_call = $this->parseVariable('MyClass::{$a}()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('MyClass', (string) $class_method_call->getClassName());
    $this->assertEquals('{$a}', (string) $class_method_call->getMethodName());

    $array_lookup = $this->parseVariable('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('a()', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $function_call = $array_lookup->getArray();
    $this->assertEquals('a', $function_call->getNamespacePath());

    $class_method_call = $this->parseVariable('$class::${$f}()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('$class', (string) $class_method_call->getClassName());
    $this->assertEquals('${$f}', (string) $class_method_call->getMethodName());

    $array_lookup = $this->parseVariable('$class::${$f}[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$class::${$f}', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('$class', (string) $class_member_lookup->getClassName());
    $this->assertEquals('${$f}', (string) $class_member_lookup->getMemberName());
    $compound_var = $class_member_lookup->getMemberName();
    $this->assertEquals('$f', (string) $compound_var->getExpression());
  }

  /**
   * Test expression.
   */
  public function testExpression() {
    $this->parseExpression('$a', '\Pharborist\VariableNode');

    /** @var CompoundVariableNode $compound_var */
    $compound_var = $this->parseExpression('${$a}', '\Pharborist\CompoundVariableNode');
    $this->assertEquals('$a', (string) $compound_var->getExpression());

    /** @var ArrayLookupNode $array_lookup */
    $array_lookup = $this->parseExpression('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    $array_lookup = $this->parseExpression('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    /** @var VariableVariableNode $var_var */
    $var_var = $this->parseExpression('$$a', '\Pharborist\VariableVariableNode');
    $this->assertEquals('$a', (string) $var_var->getVariable());

    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $this->parseExpression('MyClass::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('MyClass', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $array_lookup = $this->parseExpression('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('MyClass::$a', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('MyClass', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseExpression('static::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('static', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseExpression('$c::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());

    $class_member_lookup = $this->parseExpression('$c[0]::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c[0]', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    $class_member_lookup = $this->parseExpression('$c{0}::$a', '\Pharborist\ClassMemberLookupNode');
    $this->assertEquals('$c{0}', (string) $class_member_lookup->getClassName());
    $this->assertEquals('$a', (string) $class_member_lookup->getMemberName());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());

    /** @var ObjectPropertyNode $obj_property_lookup */
    $obj_property_lookup = $this->parseExpression('$o->property', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('property', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseExpression('$o->{$a}', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('{$a}', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseExpression('$o->$a', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('$a', (string) $obj_property_lookup->getProperty());

    $obj_property_lookup = $this->parseExpression('$o->$$a', '\Pharborist\ObjectPropertyNode');
    $this->assertEquals('$o', (string) $obj_property_lookup->getObject());
    $this->assertEquals('$$a', (string) $obj_property_lookup->getProperty());
    $var_var = $obj_property_lookup->getProperty();
    $this->assertEquals('$a', (string) $var_var->getVariable());

    /** @var CallbackCallNode $callback_call */
    $callback_call = $this->parseExpression('$a()', '\Pharborist\CallbackCallNode');
    $this->assertEquals('$a', (string) $callback_call->getCallback());

    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseExpression('$o->$a()', '\Pharborist\ObjectMethodCallNode');
    $this->assertEquals('$o', (string) $obj_method_call->getObject());
    $this->assertEquals('$a', (string) $obj_method_call->getMethodName());

    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseExpression('a()', '\Pharborist\FunctionCallNode');
    $this->assertEquals('a', (string) $function_call->getNamespacePath());

    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $this->parseExpression('namespace\MyClass::a()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('namespace\MyClass', (string) $class_method_call->getClassName());
    $this->assertEquals('a', (string) $class_method_call->getMethodName());

    $class_method_call = $this->parseExpression('MyNamespace\MyClass::$a()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('MyNamespace\MyClass', $class_method_call->getClassName());
    $this->assertEquals('$a', (string) $class_method_call->getMethodName());

    $class_method_call = $this->parseExpression('MyClass::{$a}()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('MyClass', (string) $class_method_call->getClassName());
    $this->assertEquals('{$a}', (string) $class_method_call->getMethodName());

    $array_lookup = $this->parseExpression('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('a()', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $function_call = $array_lookup->getArray();
    $this->assertEquals('a', $function_call->getNamespacePath());

    $class_method_call = $this->parseExpression('$class::${$f}()', '\Pharborist\ClassMethodCallNode');
    $this->assertEquals('$class', (string) $class_method_call->getClassName());
    $this->assertEquals('${$f}', (string) $class_method_call->getMethodName());

    $array_lookup = $this->parseExpression('$class::${$f}[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$class::${$f}', (string) $array_lookup->getArray());
    $this->assertEquals('0', (string) $array_lookup->getKey());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('$class', (string) $class_member_lookup->getClassName());
    $this->assertEquals('${$f}', (string) $class_member_lookup->getMemberName());
    $compound_var = $class_member_lookup->getMemberName();
    $this->assertEquals('$f', (string) $compound_var->getExpression());

    /** @var BinaryOperationNode $binary_op */
    $binary_op = $this->parseExpression('$a = $b++', '\Pharborist\AssignNode');
    $this->assertEquals('$a', (string) $binary_op->getLeft());
    $this->assertEquals('=', (string) $binary_op->getOperator());
    $this->assertEquals('$b++', (string) $binary_op->getRight());

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
    /** @var ConstantDeclarationStatementNode $const_stmt */
    $const_stmt = $this->parseSnippet('const MY_CONST = namespace\MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
    $const = $const_stmt->getDeclarations()[0];
    $this->assertEquals('MY_CONST', (string) $const->getName());
    $this->assertEquals('namespace\MY_CONST', (string) $const->getValue());
    $this->assertInstanceOf('\Pharborist\NamespacePathNode', $const->getValue());

    $const_stmt = $this->parseSnippet('const MY_CONST = MyNamespace\MyClass::MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
    $const = $const_stmt->getDeclarations()[0];
    $this->assertEquals('MY_CONST', (string) $const->getName());
    $this->assertEquals('MyNamespace\MyClass::MY_CONST', (string) $const->getValue());
    /** @var ClassConstantLookupNode $class_const_lookup */
    $class_const_lookup = $const->getValue();
    $this->assertInstanceOf('\Pharborist\ClassConstantLookupNode', $class_const_lookup);
    $this->assertEquals('MyNamespace\MyClass', $class_const_lookup->getClassName());
    $this->assertEquals('MY_CONST', $class_const_lookup->getConstantName());

    $const_stmt = $this->parseSnippet('const MY_CONST = MyNamespace\MyClass::class;', '\Pharborist\ConstantDeclarationStatementNode');
    $const = $const_stmt->getDeclarations()[0];
    $this->assertEquals('MY_CONST', (string) $const->getName());
    $this->assertEquals('MyNamespace\MyClass::class', (string) $const->getValue());
    /** @var ClassNameScalarNode $class_scalar */
    $class_scalar = $const->getValue();
    $this->assertInstanceOf('\Pharborist\ClassNameScalarNode', $class_scalar);
    $this->assertEquals('MyNamespace\MyClass', (string) $class_scalar->getClassName());

    $const_stmt = $this->parseSnippet('const MY_CONST = static::MY_CONST;', '\Pharborist\ConstantDeclarationStatementNode');
    $const = $const_stmt->getDeclarations()[0];
    $this->assertEquals('MY_CONST', (string) $const->getName());
    $this->assertEquals('static::MY_CONST', (string) $const->getValue());
  }

  /**
   * Test dynamic class name.
   */
  public function testDynamicClassName() {
    /** @var NewNode $new */
    $new = $this->parseExpression('new $a::$b->c', '\Pharborist\NewNode');
    $this->assertEquals('$a::$b->c', (string) $new->getClassName());
    /** @var ObjectPropertyNode $obj_property */
    $obj_property = $new->getClassName();
    $this->assertEquals('$a::$b', (string) $obj_property->getObject());
    $this->assertEquals('c', (string) $obj_property->getProperty());
    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $obj_property->getObject();
    $this->assertEquals('$a', $class_member_lookup->getClassName());
    $this->assertEquals('$b', $class_member_lookup->getMemberName());
  }

  /**
   * Test function call.
   */
  public function testFunctionCall() {
    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseExpression('do_something(&$a, $b)', '\Pharborist\FunctionCallNode');
    $this->assertEquals('do_something', (string) $function_call->getNamespacePath());
    $arguments = $function_call->getArguments();
    $this->assertEquals('&$a', (string) $arguments[0]);
    $this->assertEquals('$b', (string) $arguments[1]);
  }

  /**
   * Test static variable list.
   */
  public function testStaticVariableList() {
    /** @var StaticVariableStatementNode $static_var_stmt */
    $static_var_stmt = $this->parseSnippet('static $a, $b = 1;', '\Pharborist\StaticVariableStatementNode');
    $static_vars = $static_var_stmt->getVariables();
    $this->assertEquals('$a', (string) $static_vars[0]);
    $this->assertEquals('$b', (string) $static_vars[1]->getName());
    $this->assertEquals('1', (string) $static_vars[1]->getInitialValue());
  }

  /**
   * Test (new expr) expression.
   */
  public function testParenNewExpression() {
    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseExpression('(new $class($a, $b))->$method()', '\Pharborist\ObjectMethodCallNode');
    $this->assertEquals('(new $class($a, $b))', (string) $obj_method_call->getObject());
    $this->assertEquals('$method', (string) $obj_method_call->getMethodName());
  }

  /**
   * Test anonymous function.
   */
  public function testAnonymousFunction() {
    /** @var AnonymousFunctionNode $function */
    $function = $this->parseExpression('function(){}', '\Pharborist\AnonymousFunctionNode');
    $this->assertEquals(0, count($function->getParameters()));

    $function = $this->parseExpression('function($a, $b) use ($x, $y) { }', '\Pharborist\AnonymousFunctionNode');
    $parameters = $function->getParameters();
    $this->assertEquals(2, count($parameters));
    $this->assertEquals('$a', (string) $parameters[0]);
    $this->assertEquals('$b', (string) $parameters[1]);
    $lexical_vars = $function->getLexicalVariables();
    $this->assertEquals(2, count($lexical_vars));
    $this->assertEquals('$x', (string) $lexical_vars[0]);
    $this->assertEquals('$y', (string) $lexical_vars[1]);

    /** @var AssignNode $assign */
    $assign = $this->parseExpression('$f = function($a, $b) use ($x, $y) { }', '\Pharborist\AssignNode');
    $this->assertEquals('$f', (string) $assign->getLeft());
    $this->assertInstanceOf('\Pharborist\AnonymousFunctionNode', $assign->getRight());
    $function = $assign->getRight();
    $parameters = $function->getParameters();
    $this->assertEquals(2, count($parameters));
    $this->assertEquals('$a', (string) $parameters[0]);
    $this->assertEquals('$b', (string) $parameters[1]);
    $lexical_vars = $function->getLexicalVariables();
    $this->assertEquals(2, count($lexical_vars));
    $this->assertEquals('$x', (string) $lexical_vars[0]);
    $this->assertEquals('$y', (string) $lexical_vars[1]);
  }

  /**
   * Test iteration of tokens.
   */
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

  /**
   * Test handling embedded doc comments.
   */
  public function testEmbeddedDocComments() {
    $this->parseSnippet('/** start */ 1 /** plus before */ + /** plus after */ 2 + /** ( */ ( /** open */ 3 * 2 /** close */ ) /** end */; /** end line */', '\Pharborist\ExpressionStatementNode');
  }

  /**
   * Test doc comment on non structural element.
   */
  public function testDocCommentNonStructural() {
    $this->parseSnippet('/** doc comment */ use Test;', '\Pharborist\DocCommentNode');
  }

  /**
   * Test doc comment after empty statement.
   */
  public function testEmptyStatementBeforeDocComment() {
    $empty_statement = $this->parseSnippet('; /** function */ function test() { }', '\Pharborist\EmptyStatementNode');
    /** @var FunctionDeclarationNode $function */
    $function = $empty_statement->nextSibling()->nextSibling();
    $this->assertInstanceOf('\Pharborist\FunctionDeclarationNode', $function);
    $this->assertEquals('/** function */', $function->getDocComment());
  }

  /**
   * Test template file.
   */
  public function testTemplate() {
    $source = <<<'EOF'
<p>This is a template file</p>
<p>Hello, <?=$name?>. Welcome to <?=$lego . 'world'?>!</p>
<?php
code();
?><h1>End of template</h1><?php more_code();
EOF;
    $tree = Parser::parseSource($source);
    $this->assertEquals($source, (string) $tree);
    /** @var TemplateNode[] $templates */
    $templates = $tree->find('\Pharborist\TemplateNode');
    $template = $templates[0];
    $this->assertEquals(5, $template->getChildCount());
    /** @var EchoTagStatementNode $echo_tag */
    $echo_tag = $template->getFirst()->nextSibling();
    $this->assertInstanceOf('\Pharborist\EchoTagStatementNode', $echo_tag);
    $this->assertEquals('<?=$name?>', (string) $echo_tag);
    $expressions = $echo_tag->getExpressions();
    $this->assertEquals('$name', (string) $expressions[0]);
    $template = $templates[1];
    $this->assertEquals('?><h1>End of template</h1><?php ', (string) $template);
  }

  /**
   * Tests break statement.
   */
  public function testBreak() {
    $snippet = <<<'EOF'
while (TRUE) {
  if ($first_test) {
    break;
  }
  if ($second_test) {
    break 1;
  }
  if ($third_test) {
    break(1);
  }
  while ($fourth_test) {
    break 2;
  }
}
EOF;
    /** @var WhileNode $while */
    $while = $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    /** @var StatementBlockNode $stmt_block */
    $stmt_block = $while->getBody();
    $statements = $stmt_block->getStatements();
    /** @var IfNode $if */
    $if = $statements[0];
    $stmt_block = $if->getThen();
    /** @var BreakStatementNode $break */
    $break = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\BreakStatementNode', $break);
    $this->assertNull($break->getLevel());

    $if = $statements[1];
    $stmt_block = $if->getThen();
    $break = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\BreakStatementNode', $break);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $break->getLevel());
    $this->assertEquals('1', (string) $break->getLevel());

    $if = $statements[2];
    $stmt_block = $if->getThen();
    $break = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\BreakStatementNode', $break);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $break->getLevel());
    $this->assertEquals('1', (string) $break->getLevel());

    $while = $statements[3];
    $stmt_block = $while->getBody();
    $break = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\BreakStatementNode', $break);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $break->getLevel());
    $this->assertEquals('2', (string) $break->getLevel());
  }

  /**
   * Tests continue statement.
   */
  public function testContinue() {
    $snippet = <<<'EOF'
while (TRUE) {
  if ($first_test) {
    continue;
  }
  if ($second_test) {
    continue 1;
  }
  if ($third_test) {
    continue(1);
  }
  while ($fourth_test) {
    continue 2;
  }
}
EOF;
    /** @var WhileNode $while */
    $while = $this->parseSnippet($snippet, '\Pharborist\WhileNode');
    /** @var StatementBlockNode $stmt_block */
    $stmt_block = $while->getBody();
    $statements = $stmt_block->getStatements();
    /** @var IfNode $if */
    $if = $statements[0];
    $stmt_block = $if->getThen();
    /** @var ContinueStatementNode $continue */
    $continue = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\ContinueStatementNode', $continue);
    $this->assertNull($continue->getLevel());

    $if = $statements[1];
    $stmt_block = $if->getThen();
    $continue = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\ContinueStatementNode', $continue);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $continue->getLevel());
    $this->assertEquals('1', (string) $continue->getLevel());

    $if = $statements[2];
    $stmt_block = $if->getThen();
    $continue = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\ContinueStatementNode', $continue);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $continue->getLevel());
    $this->assertEquals('1', (string) $continue->getLevel());

    $while = $statements[3];
    $stmt_block = $while->getBody();
    $continue = $stmt_block->getStatements()[0];
    $this->assertInstanceOf('\Pharborist\ContinueStatementNode', $continue);
    $this->assertInstanceOf('\Pharborist\IntegerNode', $continue->getLevel());
    $this->assertEquals('2', (string) $continue->getLevel());
  }

  /**
   * Test global statement.
   */
  public function testGlobal() {
    $snippet = <<<'EOF'
global $a, $$b, ${expr()};
EOF;
    /** @var GlobalStatementNode $global_statement */
    $global_statement = $this->parseSnippet($snippet, '\Pharborist\GlobalStatementNode');
    $variables = $global_statement->getVariables();
    $this->assertEquals('$a', (string) $variables[0]);
    $this->assertEquals('$$b', (string) $variables[1]);
    $this->assertEquals('${expr()}', (string) $variables[2]);
  }

  /**
   * Test echo statement.
   */
  public function testEcho() {
    $snippet = <<<'EOF'
echo $a, expr(), PHP_EOL;
EOF;
    /** @var EchoStatementNode $echo */
    $echo = $this->parseSnippet($snippet, '\Pharborist\EchoStatementNode');
    $expressions = $echo->getExpressions();
    $this->assertEquals('$a', (string) $expressions[0]);
    $this->assertEquals('expr()', (string) $expressions[1]);
    $this->assertEquals('PHP_EOL', (string) $expressions[2]);
  }

  /**
   * Test goto.
   */
  public function testGoto() {
    $snippet = <<<'EOF'
loop:
  goto loop;
EOF;
    $tree = $this->parseSnippetBlock($snippet);
    /** @var GotoLabelNode $goto_label */
    $goto_label = $tree->getFirst();
    $this->assertInstanceOf('\Pharborist\GotoLabelNode', $goto_label);
    $this->assertEquals('loop', (string) $goto_label->getLabel());
    /** @var GotoStatementNode $goto_statement */
    $goto_statement = $tree->getLast();
    $this->assertInstanceOf('\Pharborist\GotoStatementNode', $goto_statement);
    $this->assertEquals('loop', (string) $goto_statement->getLabel());
  }

  /**
   * Test return statement.
   */
  public function testReturn() {
    $snippet = <<<'EOF'
function test() {
  if ($first_test) return;
  return $done;
}
EOF;
    /** @var FunctionDeclarationNode $function_declaration */
    $function_declaration = $this->parseSnippet($snippet, '\Pharborist\FunctionDeclarationNode');
    /** @var StatementBlockNode $stmt_block */
    $stmt_block = $function_declaration->getBody();
    $statements = $stmt_block->getStatements();
    /** @var IfNode $if */
    $if = $statements[0];

    /** @var ReturnStatementNode $return_statement */
    $return_statement = $if->getThen();
    $this->assertInstanceOf('\Pharborist\ReturnStatementNode', $return_statement);
    $this->assertNull($return_statement->getExpression());

    $return_statement = $statements[1];
    $this->assertInstanceOf('\Pharborist\ReturnStatementNode', $return_statement);
    $this->assertEquals('$done', $return_statement->getExpression());
  }

  /**
   * Test list.
   */
  public function testList() {
    /** @var AssignNode $assign */
    $assign = $this->parseExpression('list($a, $b) = [1, 2]', '\Pharborist\AssignNode');
    /** @var ListNode $list */
    $list = $assign->getLeft();
    $this->assertInstanceOf('\Pharborist\ListNode', $list);
    $arguments = $list->getArguments();
    $this->assertEquals('$a', (string) $arguments[0]);
    $this->assertEquals('$b', (string) $arguments[1]);
  }

  /**
   * Test throw statement.
   */
  public function testThrow() {
    /** @var ThrowStatementNode $throw */
    $throw = $this->parseSnippet('throw $e;', '\Pharborist\ThrowStatementNode');
    $this->assertEquals('$e', $throw->getExpression());
  }
}
