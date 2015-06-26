<?php
namespace Pharborist;

use Pharborist\Constants\ConstantDeclarationStatementNode;
use Pharborist\Constants\ConstantNode;
use Pharborist\ControlStructures\BreakStatementNode;
use Pharborist\ControlStructures\ContinueStatementNode;
use Pharborist\ControlStructures\DeclareNode;
use Pharborist\ControlStructures\DoWhileNode;
use Pharborist\ControlStructures\ExitNode;
use Pharborist\ControlStructures\ForeachNode;
use Pharborist\ControlStructures\ForNode;
use Pharborist\ControlStructures\GotoLabelNode;
use Pharborist\ControlStructures\GotoStatementNode;
use Pharborist\ControlStructures\IfNode;
use Pharborist\ControlStructures\ImportNode;
use Pharborist\ControlStructures\ReturnStatementNode;
use Pharborist\ControlStructures\SwitchNode;
use Pharborist\ControlStructures\WhileNode;
use Pharborist\Exceptions\ThrowStatementNode;
use Pharborist\Exceptions\TryCatchNode;
use Pharborist\Functions\AnonymousFunctionNode;
use Pharborist\Functions\CallbackCallNode;
use Pharborist\Functions\DefineNode;
use Pharborist\Functions\EmptyNode;
use Pharborist\Functions\EvalNode;
use Pharborist\Functions\FunctionCallNode;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Functions\HaltCompilerNode;
use Pharborist\Functions\IssetNode;
use Pharborist\Functions\ListNode;
use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Namespaces\UseDeclarationBlockNode;
use Pharborist\Objects\ClassConstantLookupNode;
use Pharborist\Objects\ClassMemberListNode;
use Pharborist\Objects\ClassMemberLookupNode;
use Pharborist\Objects\ClassMethodCallNode;
use Pharborist\Objects\ClassMethodNode;
use Pharborist\Objects\ClassNode;
use Pharborist\Objects\InterfaceMethodNode;
use Pharborist\Objects\InterfaceNode;
use Pharborist\Objects\NewNode;
use Pharborist\Objects\ObjectMethodCallNode;
use Pharborist\Objects\ObjectPropertyNode;
use Pharborist\Objects\TraitAliasNode;
use Pharborist\Objects\TraitNode;
use Pharborist\Objects\TraitPrecedenceNode;
use Pharborist\Objects\TraitUseNode;
use Pharborist\Operators\BinaryOperationNode;
use Pharborist\Operators\TernaryOperationNode;
use Pharborist\Operators\UnaryOperationNode;
use Pharborist\Types\ArrayNode;
use Pharborist\Types\ArrayPairNode;
use Pharborist\Types\FalseNode;
use Pharborist\Types\TrueNode;
use Pharborist\Variables\CompoundVariableNode;
use Pharborist\Variables\GlobalStatementNode;
use Pharborist\Variables\ReferenceVariableNode;
use Pharborist\Variables\StaticVariableStatementNode;
use Pharborist\Variables\VariableVariableNode;

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
    $this->assertCount(1, $tree->children(Filter::isInstanceOf('\Pharborist\Functions\FunctionDeclarationNode')));
    // Test with a non-existant file.
    $tree = Parser::parseFile('no-such-file.php');
    $this->assertFalse($tree);
  }

  /**
   * Helper function to parse source.
   */
  public function parseSource($source) {
    $tree = Parser::parseSource($source);
    $this->assertEquals($source, $tree->getText());
    return $tree;
  }

  /**
   * Helper function to parse a snippet.
   */
  public function parseSnippet($snippet, $expected_type) {
    $tree = $this->parseSource('<?php ' . $snippet);
    $node = $tree->firstChild()->next();
    $this->assertInstanceOf($expected_type, $node);
    return $node;
  }

  /**
   * Test parsing empty source file.
   */
  public function testParseEmpty() {
    $tree = Parser::parseSource('');
    $this->assertEquals(0, $tree->childCount());
  }

  /**
   * Test parsing php file with no code.
   */
  public function testParseBlank() {
    $tree = Parser::parseSource("<?php\n");
    $this->assertEquals(1, $tree->childCount());
    $this->assertInstanceOf('\Pharborist\TokenNode', $tree->firstChild());
    /** @var TokenNode $child */
    $child = $tree->firstChild();
    $this->assertEquals(T_OPEN_TAG, $child->getType());
  }

  /**
   * Test parsing namespace.
   */
  public function testNamespace() {
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('/** test */ namespace MyNamespace\Test ; body();', '\Pharborist\Namespaces\NamespaceNode');
    $this->assertEquals('/** test */', $namespace_node->getDocComment()->getText());
    $this->assertEquals('MyNamespace\Test', $namespace_node->getName()->getText());
    $this->assertEquals('body();', $namespace_node->getBody()->getText());

    // Test with body
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace MyNamespace\Test\Body { }', '\Pharborist\Namespaces\NamespaceNode');
    $this->assertEquals('MyNamespace\Test\Body', $namespace_node->getName()->getText());
    $this->assertNotNull($namespace_node->getBody());

    // Test global
    /** @var NamespaceNode $namespace_node */
    $namespace_node = $this->parseSnippet('namespace { }', '\Pharborist\Namespaces\NamespaceNode');
    $this->assertNull($namespace_node->getName());
    $this->assertNotNull($namespace_node->getBody());
  }

  /**
   * Test parsing use declarations.
   */
  public function testUseDeclaration() {
    /** @var UseDeclarationBlockNode $use_block */
    $use_block = $this->parseSnippet(
      'use MyNamespace\MyClass as MyAlias ;',
      '\Pharborist\Namespaces\UseDeclarationBlockNode'
    );
    $use_declaration_statement = $use_block->getDeclarationStatements()[0];
    $use_declaration = $use_declaration_statement->getDeclarations()[0];
    $this->assertEquals('MyNamespace\MyClass', $use_declaration->getName()->getText());
    $this->assertEquals('MyAlias', $use_declaration->getAlias()->getText());
    $this->assertEquals('MyNamespace\MyClass as MyAlias', $use_declaration->getText());
  }

  /**
   * Test parsing function declaration.
   */
  public function testFunctionDeclaration() {
    /** @var FunctionDeclarationNode $function_declaration */
    $function_declaration = $this->parseSnippet(
      'function my_func(array $a, callable $b, namespace\Test $c, \MyNamespace\Test $d, $e = 1, &$f, $g) { }',
      '\Pharborist\Functions\FunctionDeclarationNode'
    );
    $this->assertNull($function_declaration->getReference());
    $this->assertEquals('my_func', $function_declaration->getName()->getText());
    $parameters = $function_declaration->getParameters();

    $parameter = $parameters[0];
    $this->assertEquals('a', $parameter->getName());
    $this->assertEquals('$a', $parameter->getVariable()->getText());
    $this->assertEquals('array', $parameter->getTypeHint()->getText());

    $parameter = $parameters[1];
    $this->assertEquals('b', $parameter->getName());
    $this->assertEquals('$b', $parameter->getVariable()->getText());
    $this->assertEquals('callable', $parameter->getTypeHint()->getText());

    $parameter = $parameters[2];
    $this->assertEquals('c', $parameter->getName());
    $this->assertEquals('$c', $parameter->getVariable()->getText());
    $this->assertEquals('namespace\Test', $parameter->getTypeHint()->getText());

    $parameter = $parameters[3];
    $this->assertEquals('d', $parameter->getName());
    $this->assertEquals('$d', $parameter->getVariable()->getText());
    $this->assertEquals('\MyNamespace\Test', $parameter->getTypeHint()->getText());

    $parameter = $parameters[4];
    $this->assertEquals('e', $parameter->getName());
    $this->assertEquals('$e', $parameter->getVariable()->getText());
    $this->assertEquals('1', $parameter->getValue()->getText());

    $parameter = $parameters[5];
    $this->assertEquals('f', $parameter->getName());
    $this->assertEquals('$f', $parameter->getVariable()->getText());
    $this->assertEquals('&', $parameter->getReference()->getText());

    $parameter = $parameters[6];
    $this->assertEquals('g', $parameter->getName());
    $this->assertEquals('$g', $parameter->getVariable()->getText());
    $this->assertNull($parameter->getReference());

    $parameter->setReference(TRUE);
    $this->assertEquals('&', $parameter->getReference()->getText());

    $function_declaration->setReference(TRUE);
    $this->assertEquals('&', $function_declaration->getReference()->getText());
  }

  /**
   * Test parsing const declaration.
   */
  public function testConstDeclaration() {
    /** @var ConstantDeclarationStatementNode $const_declaration_list */
    $const_declaration_list = $this->parseSnippet('const MyConst = 1;', '\Pharborist\Constants\ConstantDeclarationStatementNode');
    $const_declaration = $const_declaration_list->getDeclarations()[0];
    $this->assertEquals('MyConst', $const_declaration->getName()->getText());
    $this->assertEquals('1', $const_declaration->getValue()->getText());
  }

  /**
   * Test parsing top level halt compiler.
   */
  public function testHaltCompiler() {
    /** @var HaltCompilerNode $node */
    $node = $this->parseSnippet('__halt_compiler();', '\Pharborist\Functions\HaltCompilerNode');
    $this->assertEquals('__halt_compiler', $node->getName()->getText());
    $this->assertEquals("__halt_compiler();", $node->getText());
  }

  /**
   * Test inner halt compiler is an error.
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage __halt_compiler can only be used from the outermost scope
   */
  public function testInnerHaltCompiler() {
    $this->parseSnippet("{ __halt_compiler(); }", '\Pharborist\Functions\HaltCompilerNode');
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
  /** property doc comment */
  public $publicProperty = 1;
  protected $protectedProperty;
  private $privateProperty;
  static public $classProperty;
  var $backwardsCompatibility;

  /** method doc comment. */
  public function myMethod($a, $b) { perform(); }

  final public function noOverride() {
  }

  static public function classMethod() {
  }

  abstract public function mustImplement();

  function noVisibility() {
  }

  use A, B, C {
    B::smallTalk insteadof A;
    A::bigTalk insteadof B, C;
    B::bigTalk as talk;
    sayHello as protected;
  }
}
EOF;
    /** @var ClassNode $class_declaration */
    $class_declaration = $this->parseSnippet($snippet, '\Pharborist\Objects\ClassNode');
    $this->assertEquals('/** Class doc comment. */', $class_declaration->getDocComment()->getText());
    $this->assertEquals('MyClass', $class_declaration->getName()->getText());
    $this->assertNull($class_declaration->getFinal());
    $this->assertEquals('abstract', $class_declaration->getAbstract()->getText());
    $this->assertEquals('ParentClass', $class_declaration->getExtends()->getText());
    $implements = $class_declaration->getImplements();
    $this->assertEquals('SomeInterface', $implements[0]->getText());
    $this->assertEquals('AnotherInterface', $implements[1]->getText());
    $statements = $class_declaration->getStatements();

    /** @var ConstantDeclarationStatementNode $const_statement */
    $const_statement = $statements[0];
    $this->assertInstanceOf('\Pharborist\Constants\ConstantDeclarationStatementNode', $const_statement);
    $this->assertEquals('/** const doc comment */', $const_statement->getDocComment()->getText());

    /** @var ClassMemberListNode $class_member_list */
    $class_member_list = $statements[1];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $class_member_list);
    $this->assertEquals('/** property doc comment */', $class_member_list->getDocComment());
    $this->assertEquals('public', $class_member_list->getVisibility()->getText());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$publicProperty', $class_member->getName()->getText());
    $this->assertEquals('1', $class_member->getValue()->getText());

    $class_member_list = $statements[2];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $class_member_list);
    $this->assertEquals('protected', $class_member_list->getVisibility()->getText());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$protectedProperty', $class_member->getName()->getText());

    $class_member_list = $statements[3];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $class_member_list);
    $this->assertEquals('private', $class_member_list->getVisibility()->getText());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$privateProperty', $class_member->getName()->getText());

    $class_member_list = $statements[4];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $class_member_list);
    $this->assertEquals('public', $class_member_list->getVisibility()->getText());
    $this->assertEquals('static', $class_member_list->getStatic()->getText());
    $class_member = $class_member_list->getMembers()[0];
    $this->assertEquals('$classProperty', $class_member->getName()->getText());

    /** @var ClassMethodNode $method */
    $method = $statements[6];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('/** method doc comment. */', $method->getDocComment()->getText());
    $this->assertNull($method->getReference());
    $this->assertEquals('myMethod', $method->getName()->getText());
    $this->assertEquals('public', $method->getVisibility()->getText());
    $parameters = $method->getParameters();
    $this->assertCount(2, $parameters);
    $this->assertEquals('$a', $parameters[0]->getText());
    $this->assertEquals('$b', $parameters[1]->getText());
    $this->assertEquals('{ perform(); }', $method->getBody()->getText());

    $method->setReference(TRUE);
    $this->assertEquals('&', $method->getReference()->getText());

    $method = $statements[7];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('noOverride', $method->getName()->getText());
    $this->assertEquals('public', $method->getVisibility()->getText());
    $this->assertEquals('final', $method->getFinal()->getText());

    $method = $statements[8];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('classMethod', $method->getName()->getText());
    $this->assertEquals('public', $method->getVisibility()->getText());
    $this->assertEquals('static', $method->getStatic()->getText());

    $method = $statements[9];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('mustImplement', $method->getName()->getText());
    $this->assertEquals('public', $method->getVisibility()->getText());
    $this->assertEquals('abstract', $method->getAbstract()->getText());

    $method = $statements[10];
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodNode', $method);
    $this->assertEquals('noVisibility', $method->getName()->getText());
    $this->assertNull($method->getVisibility());

    /** @var TraitUseNode $trait_use */
    $trait_use = $statements[11];
    $traits = $trait_use->getTraits();
    $this->assertInstanceOf('\Pharborist\Objects\TraitUseNode', $trait_use);
    $this->assertEquals('A', $traits[0]->getText());
    $this->assertEquals('B', $traits[1]->getText());
    $this->assertEquals('C', $traits[2]->getText());

    $adaptations = $trait_use->getAdaptations();
    /** @var TraitPrecedenceNode $trait_precedence */
    $trait_precedence = $adaptations[0];
    $this->assertInstanceOf('\Pharborist\Objects\TraitPrecedenceNode', $trait_precedence);
    $this->assertInstanceOf('\Pharborist\Objects\TraitMethodReferenceNode', $trait_precedence->getTraitMethodReference());
    $this->assertEquals('B::smallTalk', $trait_precedence->getTraitMethodReference()->getText());
    $this->assertEquals('B', $trait_precedence->getTraitMethodReference()->getTraitName()->getText());
    $this->assertEquals('smallTalk', $trait_precedence->getTraitMethodReference()->getMethodReference()->getText());
    $this->assertEquals('A', $trait_precedence->getTraitNames()[0]->getText());

    $trait_precedence = $adaptations[1];
    $this->assertInstanceOf('\Pharborist\Objects\TraitPrecedenceNode', $trait_precedence);
    $this->assertInstanceOf('\Pharborist\Objects\TraitMethodReferenceNode', $trait_precedence->getTraitMethodReference());
    $this->assertEquals('A::bigTalk', $trait_precedence->getTraitMethodReference()->getText());
    $this->assertEquals('A', $trait_precedence->getTraitMethodReference()->getTraitName()->getText());
    $this->assertEquals('bigTalk', $trait_precedence->getTraitMethodReference()->getMethodReference()->getText());
    $trait_names = $trait_precedence->getTraitNames();
    $this->assertEquals('B', $trait_names[0]->getText());
    $this->assertEquals('C', $trait_names[1]->getText());

    /** @var TraitAliasNode $trait_alias */
    $trait_alias = $adaptations[2];
    $this->assertInstanceOf('\Pharborist\Objects\TraitAliasNode', $trait_alias);
    $this->assertInstanceOf('\Pharborist\Objects\TraitMethodReferenceNode', $trait_alias->getTraitMethodReference());
    $this->assertEquals('B::bigTalk', $trait_alias->getTraitMethodReference()->getText());
    $this->assertEquals('B', $trait_alias->getTraitMethodReference()->getTraitName()->getText());
    $this->assertEquals('bigTalk', $trait_alias->getTraitMethodReference()->getMethodReference()->getText());
    $this->assertEquals('talk', $trait_alias->getAlias()->getText());

    $trait_alias = $adaptations[3];
    $this->assertInstanceOf('\Pharborist\Objects\TraitAliasNode', $trait_alias);
    $this->assertInstanceOf('\Pharborist\Namespaces\NameNode', $trait_alias->getTraitMethodReference());
    $this->assertEquals('sayHello', $trait_alias->getTraitMethodReference()->getText());
    $this->assertEquals('protected', $trait_alias->getVisibility()->getText());
  }

  /**
   * Test interface declaration.
   */
  public function testInterfaceDeclaration() {
    $snippet = <<<'EOF'
/** interface */
interface MyInterface extends SomeInterface, AnotherInterface {
  const MY_CONST = 1;
  /** interface method */
  public function myMethod($a, $b);
}
EOF;
    /** @var InterfaceNode $interface_declaration */
    $interface_declaration = $this->parseSnippet($snippet, '\Pharborist\Objects\InterfaceNode');
    $this->assertEquals('/** interface */', $interface_declaration->getDocComment()->getText());
    $this->assertEquals('MyInterface', $interface_declaration->getName()->getText());
    $extends = $interface_declaration->getExtends();
    $this->assertEquals('SomeInterface', $extends[0]->getText());
    $this->assertEquals('AnotherInterface', $extends[1]->getText());
    $statements = $interface_declaration->getStatements();

    /** @var ConstantDeclarationStatementNode $constant_declaration_statement */
    $constant_declaration_statement = $statements[0];
    $this->assertInstanceOf('\Pharborist\Constants\ConstantDeclarationStatementNode', $constant_declaration_statement);
    $constant_declaration = $constant_declaration_statement->getDeclarations()[0];
    $this->assertEquals('MY_CONST', $constant_declaration->getName()->getText());
    $this->assertEquals('1', $constant_declaration->getValue()->getText());

    /** @var InterfaceMethodNode $method */
    $method = $statements[1];
    $this->assertEquals('/** interface method */', $method->getDocComment()->getText());
    $this->assertNull($method->getReference());
    $this->assertEquals('myMethod', $method->getName()->getText());
    $this->assertEquals('public', $method->getVisibility()->getText());
    $this->assertNull($method->getStatic());
    $parameters = $method->getParameters();
    $this->assertCount(2, $parameters);
    $this->assertEquals('$a', $parameters[0]->getText());
    $this->assertEquals('$b', $parameters[1]->getText());

    $method->setReference(TRUE);
    $this->assertEquals('&', $method->getReference()->getText());

    $method->setStatic(TRUE);
    $this->assertEquals('static', $method->getStatic()->getText());
  }

  /**
   * Test trait declaration.
   */
  public function testTraitDeclaration() {
    $snippet = <<<'EOF'
/** trait doc comment */
trait MyTrait {
  // trait statements are covered by testClassDeclaration
  const MY_CONST = 1;
}
EOF;
    /** @var TraitNode $trait_declaration */
    $trait_declaration = $this->parseSnippet($snippet, '\Pharborist\Objects\TraitNode');
    $this->assertEquals('/** trait doc comment */', $trait_declaration->getDocComment()->getText());
    $this->assertEquals('MyTrait', $trait_declaration->getName()->getText());

    $statements = $trait_declaration->getStatements();
    /** @var ConstantDeclarationStatementNode $const_stmt */
    $const_stmt = $statements[0];
    $const = $const_stmt->getDeclarations()[0];
    $this->assertEquals('MY_CONST', $const->getName()->getText());
  }

  /**
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage Traits can only be composed from other traits with the 'use' keyword.
   */
  public function testTraitExtends() {
    $snippet = 'trait MyTrait extends BaseTrait {}' . PHP_EOL;
    $this->parseSnippet($snippet, '\Pharborist\Objects\TraitNode');
  }

  /**
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage Traits can not implement interfaces.
   */
  public function testTraitImplements() {
    $snippet = 'trait MyTrait implements TestInterface {}' . PHP_EOL;
    $this->parseSnippet($snippet, '\Pharborist\Objects\TraitNode');
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
    $if = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\IfNode');
    $this->assertEquals('$condition', $if->getCondition()->getText());
    $this->assertEquals('{ then(); }', $if->getThen()->getText());
    $else_ifs = $if->getElseIfs();
    $this->assertCount(2, $else_ifs);
    $this->assertEquals('$other_condition', $else_ifs[0]->getCondition()->getText());
    $this->assertEquals('{ other_then(); }', $else_ifs[0]->getThen()->getText());
    $this->assertEquals('$another_condition', $else_ifs[1]->getCondition()->getText());
    $this->assertEquals('{ do_else(); }', $if->getElse()->getText());
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
    $if = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\IfNode');
    $this->assertEquals('$condition', $if->getCondition()->getText());
    $this->assertEquals('then();', $if->getThen()->getText());
    $else_ifs = $if->getElseIfs();
    $this->assertCount(2, $else_ifs);
    $this->assertEquals('$other_condition', $else_ifs[0]->getCondition()->getText());
    $this->assertEquals('other_then();', $else_ifs[0]->getThen()->getText());
    $this->assertEquals('$another_condition', $else_ifs[1]->getCondition()->getText());
    $this->assertEquals('do_else();', $if->getElse()->getText());
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
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForeachNode');
    $this->assertEquals('$array', $foreach->getOnEach()->getText());
    $this->assertEquals('$k', $foreach->getKey()->getText());
    /** @var ReferenceVariableNode $value */
    $value = $foreach->getValue();
    $this->assertInstanceOf('\Pharborist\Variables\ReferenceVariableNode', $value);
    $this->assertEquals('&$v', $value->getText());
    $this->assertEquals('$v', $value->getVariable()->getText());
    $this->assertEquals('body();', $foreach->getBody()->getText());

    $snippet = <<<'EOF'
foreach ($array as $v)
  body();
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForeachNode');
    $this->assertEquals('$array', $foreach->getOnEach()->getText());
    $this->assertNull($foreach->getKey());
    $this->assertEquals('$v', $foreach->getValue()->getText());
    $this->assertEquals('body();', $foreach->getBody()->getText());
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
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForeachNode');
    $this->assertEquals('$array', $foreach->getOnEach()->getText());
    $this->assertEquals('$k', $foreach->getKey()->getText());
    $this->assertEquals('&$v', $foreach->getValue()->getText());
    $this->assertEquals('body();', $foreach->getBody()->getText());

    $snippet = <<<'EOF'
foreach ($array as $v):
  body();
endforeach;
EOF;
    /** @var ForeachNode $foreach */
    $foreach = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForeachNode');
    $this->assertEquals('$array', $foreach->getOnEach()->getText());
    $this->assertNull($foreach->getKey());
    $this->assertEquals('$v', $foreach->getValue()->getText());
    $this->assertEquals('body();', $foreach->getBody()->getText());
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
    $while = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\WhileNode');
    $this->assertEquals('$cond', $while->getCondition()->getText());
    $this->assertEquals('body();', $while->getBody()->getText());
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
    $while = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\WhileNode');
    $this->assertEquals('$cond', $while->getCondition()->getText());
    $this->assertEquals('body();', $while->getBody()->getText());
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
    $do_while = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\DoWhileNode');
    $this->assertEquals('body();', $do_while->getBody()->getText());
    $this->assertEquals('$cond', $do_while->getCondition()->getText());
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
    $for = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForNode');
    $this->assertCount(1, $for->getInitial()->getItems());
    $this->assertEquals('$i = 0', $for->getInitial()->getText());
    $this->assertCount(1, $for->getCondition()->getItems());
    $this->assertEquals('$i < 10', $for->getCondition()->getText());
    $this->assertCount(1, $for->getStep()->getItems());
    $this->assertEquals('++$i', $for->getStep()->getText());
    $this->assertEquals('body();', $for->getBody()->getText());
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
    $for = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForNode');
    $this->assertEquals('$i = 0', $for->getInitial()->getText());
    $this->assertEquals('$i < 10', $for->getCondition()->getText());
    $this->assertEquals('++$i', $for->getStep()->getText());
    $this->assertEquals('body();', $for->getBody()->getText());
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
    $for = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\ForNode');
    $this->assertEquals('', $for->getInitial()->getText());
    $this->assertEquals('', $for->getCondition()->getText());
    $this->assertEquals('', $for->getStep()->getText());
    $this->assertEquals('body();', $for->getBody()->getText());
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
    $switch = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\SwitchNode');
    $this->assertEquals('$cond', $switch->getSwitchOn()->getText());
    $cases = $switch->getCases();
    $case = $cases[0];
    $this->assertEquals("'a'", $case->getMatchOn()->getText());
    $this->assertEquals('break;', $case->getBody()->getText());
    $case = $cases[1];
    $this->assertEquals("'fall'", $case->getMatchOn()->getText());
    $this->assertNull($case->getBody());
    $case = $cases[2];
    $this->assertEquals("'through'", $case->getMatchOn()->getText());
    $this->assertEquals('break;', $case->getBody()->getText());
    $case = $cases[3];
    $this->assertInstanceOf('\Pharborist\ControlStructures\DefaultNode', $case);
    $this->assertEquals('break;', $case->getBody()->getText());
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
    $switch = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\SwitchNode');
    $this->assertEquals('$cond', $switch->getSwitchOn()->getText());
    $cases = $switch->getCases();
    $case = $cases[0];
    $this->assertEquals("'a'", $case->getMatchOn()->getText());
    $this->assertEquals('break;', $case->getBody()->getText());
    $case = $cases[1];
    $this->assertEquals("'fall'", $case->getMatchOn()->getText());
    $this->assertNull($case->getBody());
    $case = $cases[2];
    $this->assertEquals("'through'", $case->getMatchOn()->getText());
    $this->assertEquals('break;', $case->getBody()->getText());
    $case = $cases[3];
    $this->assertInstanceOf('\Pharborist\ControlStructures\DefaultNode', $case);
    $this->assertEquals('break;', $case->getBody()->getText());
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
    /** @var \Pharborist\Exceptions\TryCatchNode $try_catch */
    $try_catch = $this->parseSnippet($snippet, '\Pharborist\Exceptions\TryCatchNode');
    $this->assertEquals('{ try_body(); }', $try_catch->getTry()->getText());
    $catches = $try_catch->getCatches();
    $catch = $catches[0];
    $this->assertEquals('SomeException', $catch->getExceptionType()->getText());
    $this->assertEquals('$e', $catch->getVariable()->getText());
    $this->assertEquals('{ some_body(); }', $catch->getBody()->getText());
    $catch = $catches[1];
    $this->assertEquals('OtherException', $catch->getExceptionType()->getText());
    $this->assertEquals('$e', $catch->getVariable()->getText());
    $this->assertEquals('{ other_body(); }', $catch->getBody()->getText());
  }

  /**
   * Test declare.
   */
  public function testDeclare() {
    $snippet = 'declare(DECLARE_TEST = 1, MY_CONST = 2) { body(); }';
    /** @var DeclareNode $declare */
    $declare = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\DeclareNode');
    $directives = $declare->getDirectives();
    $directive = $directives[0];
    $this->assertEquals('DECLARE_TEST', $directive->getName()->getText());
    $this->assertEquals('1', $directive->getValue()->getText());
    $directive = $directives[1];
    $this->assertEquals('MY_CONST', $directive->getName()->getText());
    $this->assertEquals('2', $directive->getValue()->getText());
    $this->assertEquals('{ body(); }', $declare->getBody()->getText());
  }

  /**
   * Test declare.
   */
  public function testAlternativeDeclare() {
    $snippet = 'declare(DECLARE_TEST = 1, MY_CONST = 2): body(); enddeclare;';
    /** @var DeclareNode $declare */
    $declare = $this->parseSnippet($snippet, '\Pharborist\ControlStructures\DeclareNode');
    $directives = $declare->getDirectives();
    $directive = $directives[0];
    $this->assertEquals('DECLARE_TEST', $directive->getName()->getText());
    $this->assertEquals('1', $directive->getValue()->getText());
    $directive = $directives[1];
    $this->assertEquals('MY_CONST', $directive->getName()->getText());
    $this->assertEquals('2', $directive->getValue()->getText());
    $this->assertEquals('body();', $declare->getBody()->getText());
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
    $expression_node = $statement_node->firstChild();
    $this->assertInstanceOf($expected_type, $expression_node);
    return $expression_node;
  }

  /**
   * Helper function to parse a static expression.
   * @param string $static_expression
   * @param string $expected_type
   * @return Node
   */
  public function parseStaticExpression($static_expression, $expected_type) {
    $statement_snippet = 'const EXPR = ' . $static_expression . ';' . PHP_EOL;
    /** @var ConstantDeclarationStatementNode $statement_node */
    $statement_node = $this->parseSnippet($statement_snippet, '\Pharborist\Constants\ConstantDeclarationStatementNode');
    $declaration = $statement_node->getDeclarations()[0];
    $expression_node = $declaration->getValue();
    $this->assertInstanceOf($expected_type, $expression_node);
    return $expression_node;
  }

  /**
   * Test static expressions.
   */
  public function testStaticExpression() {
    $this->parseStaticExpression('42', '\Pharborist\Types\IntegerNode');
    $this->parseStaticExpression('4.2', '\Pharborist\Types\FloatNode');
    $this->parseStaticExpression("'hello'", '\Pharborist\Types\StringNode');
    $this->parseStaticExpression('"hello"', '\Pharborist\Types\StringNode');
    $this->parseStaticExpression('__LINE__', '\Pharborist\Constants\LineMagicConstantNode');
    $this->parseStaticExpression('__FILE__', '\Pharborist\Constants\FileMagicConstantNode');
    $this->parseStaticExpression('__DIR__', '\Pharborist\Constants\DirMagicConstantNode');
    $this->parseStaticExpression('__TRAIT__', '\Pharborist\Constants\TraitMagicConstantNode');
    $this->parseStaticExpression('__METHOD__', '\Pharborist\Constants\MethodMagicConstantNode');
    $this->parseStaticExpression('__FUNCTION__', '\Pharborist\Constants\FunctionMagicConstantNode');
    $this->parseStaticExpression('__NAMESPACE__', '\Pharborist\Constants\NamespaceMagicConstantNode');
    $this->parseStaticExpression('__CLASS__', '\Pharborist\Constants\ClassMagicConstantNode');

    $snippet = '<<<EOF
EOF';
    $this->parseStaticExpression($snippet, '\Pharborist\Types\HeredocNode');

    $snippet = '<<<EOF
test
EOF';
    $this->parseStaticExpression($snippet, '\Pharborist\Types\HeredocNode');

    //@todo test contents of heredoc
    $snippet = '<<<\'EOF\'
test
EOF';
    $this->parseStaticExpression($snippet, '\Pharborist\Types\HeredocNode'); //@todo NowDocNode

    /** @var ConstantNode $const */
    $const = $this->parseStaticExpression('namespace\MY_CONST', '\Pharborist\Constants\ConstantNode');
    $this->assertEquals('namespace\MY_CONST', $const->getConstantName()->getText());

    /** @var ClassConstantLookupNode $class_constant_lookup */
    $class_constant_lookup = $this->parseStaticExpression('MyNamespace\MyClass::MY_CONST', '\Pharborist\Objects\ClassConstantLookupNode');
    $this->assertEquals('MyNamespace\MyClass', $class_constant_lookup->getClassName()->getText());
    $this->assertEquals('MY_CONST', $class_constant_lookup->getConstantName()->getText());

    $class_constant_lookup = $this->parseStaticExpression('static::MY_CONST', '\Pharborist\Objects\ClassConstantLookupNode');
    $this->assertEquals('static', $class_constant_lookup->getClassName()->getText());
    $this->assertEquals('MY_CONST', $class_constant_lookup->getConstantName()->getText());

    $class_constant_lookup = $this->parseStaticExpression('MyClass::class', '\Pharborist\Objects\ClassNameScalarNode');
    $this->assertEquals('MyClass', $class_constant_lookup->getClassName()->getText());

    $class_constant_lookup = $this->parseStaticExpression('static::class', '\Pharborist\Objects\ClassNameScalarNode');
    $this->assertEquals('static', $class_constant_lookup->getClassName()->getText());

    $this->parseStaticExpression('1 + 2', '\Pharborist\Operators\AddNode');
    $this->parseStaticExpression('1 - 2', '\Pharborist\Operators\SubtractNode');
    $this->parseStaticExpression('1 * 2', '\Pharborist\Operators\MultiplyNode');
    $this->parseStaticExpression('1 / 2', '\Pharborist\Operators\DivideNode');
    $this->parseStaticExpression('1 % 2', '\Pharborist\Operators\ModulusNode');
    $this->parseStaticExpression('!2', '\Pharborist\Operators\BooleanNotNode');
    $this->parseStaticExpression('~2', '\Pharborist\Operators\BitwiseNotNode');
    $this->parseStaticExpression('1 | 2', '\Pharborist\Operators\BitwiseOrNode');
    $this->parseStaticExpression('1 & 2', '\Pharborist\Operators\BitwiseAndNode');
    $this->parseStaticExpression('1 ^ 2', '\Pharborist\Operators\BitwiseXorNode');
    $this->parseStaticExpression('1 << 2', '\Pharborist\Operators\BitwiseShiftLeftNode');
    $this->parseStaticExpression('1 >> 2', '\Pharborist\Operators\BitwiseShiftRightNode');
    $this->parseStaticExpression("'a' . 'b'", '\Pharborist\Operators\ConcatNode');
    $this->parseStaticExpression('1 or 2', '\Pharborist\Operators\LogicalOrNode');
    $this->parseStaticExpression('1 xor 2', '\Pharborist\Operators\LogicalXorNode');
    $this->parseStaticExpression('1 and 2', '\Pharborist\Operators\LogicalAndNode');
    $this->parseStaticExpression('1 && 2', '\Pharborist\Operators\BooleanAndNode');
    $this->parseStaticExpression('1 || 2', '\Pharborist\Operators\BooleanOrNode');
    $this->parseStaticExpression('1 === 2', '\Pharborist\Operators\IdenticalNode');
    $this->parseStaticExpression('1 !== 2', '\Pharborist\Operators\NotIdenticalNode');
    $this->parseStaticExpression('1 == 2', '\Pharborist\Operators\EqualNode');
    $this->parseStaticExpression('1 != 2', '\Pharborist\Operators\NotEqualNode');
    $this->parseStaticExpression('1 < 2', '\Pharborist\Operators\LessThanNode');
    $this->parseStaticExpression('1 <= 2', '\Pharborist\Operators\LessThanOrEqualToNode');
    $this->parseStaticExpression('1 > 2', '\Pharborist\Operators\GreaterThanNode');
    $this->parseStaticExpression('1 >= 2', '\Pharborist\Operators\GreaterThanOrEqualToNode');
    $this->parseStaticExpression('+1', '\Pharborist\Operators\PlusNode');
    /** @var UnaryOperationNode $unary */
    $unary = $this->parseStaticExpression('-1', '\Pharborist\Operators\NegateNode');
    $this->assertEquals('-', $unary->getOperator());
    $this->assertEquals('1', $unary->getOperand());
    $this->parseStaticExpression('1 ?: 2', '\Pharborist\Operators\ElvisNode');
    $this->parseStaticExpression('1 ? 2 : 3', '\Pharborist\Operators\TernaryOperationNode');
    /** @var ParenthesisNode $paren */
    $paren = $this->parseStaticExpression('(1)', '\Pharborist\ParenthesisNode');
    $this->assertEquals('1', $paren->getExpression()->getText());
  }

  /**
   * Test array.
   */
  public function testArray() {
    /** @var ArrayNode $array */
    $array = $this->parseExpression('array(3, 5, 8, )', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', $elements[0]->getText());
    $this->assertEquals('5', $elements[1]->getText());
    $this->assertEquals('8', $elements[2]->getText());

    $array = $this->parseExpression('[3, 5, 8]', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', $elements[0]->getText());
    $this->assertEquals('5', $elements[1]->getText());
    $this->assertEquals('8', $elements[2]->getText());

    $array = $this->parseExpression('array("a" => 1, "b" => 2)', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    /** @var ArrayPairNode $pair */
    $pair = $elements[0];
    $this->assertEquals('"a"', $pair->getKey()->getText());
    $this->assertEquals('1', $pair->getValue()->getText());
    $pair = $elements[1];
    $this->assertEquals('"b"', $pair->getKey()->getText());
    $this->assertEquals('2', $pair->getValue()->getText());

    $array = $this->parseExpression('["a" => 1, "b" => 2]', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $pair = $elements[0];
    $this->assertEquals('"a"', $pair->getKey()->getText());
    $this->assertEquals('1', $pair->getValue()->getText());
    $pair = $elements[1];
    $this->assertEquals('"b"', $pair->getKey()->getText());
    $this->assertEquals('2', $pair->getValue()->getText());

    $array = $this->parseExpression('[&$a, "k" => &$v]', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('&$a', $elements[0]->getText());
    $pair = $elements[1];
    $this->assertEquals('"k"', $pair->getKey()->getText());
    $this->assertEquals('&$v', $pair->getValue()->getText());

    $array = $this->parseStaticExpression('array(3, 5, 8, )', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', $elements[0]->getText());
    $this->assertEquals('5', $elements[1]->getText());
    $this->assertEquals('8', $elements[2]->getText());

    $array = $this->parseStaticExpression('[3, 5, 8]', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $this->assertEquals('3', $elements[0]->getText());
    $this->assertEquals('5', $elements[1]->getText());
    $this->assertEquals('8', $elements[2]->getText());

    $array = $this->parseStaticExpression('array("a" => 1, "b" => 2)', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    /** @var ArrayPairNode $pair */
    $pair = $elements[0];
    $this->assertEquals('"a"', $pair->getKey()->getText());
    $this->assertEquals('1', $pair->getValue()->getText());
    $pair = $elements[1];
    $this->assertEquals('"b"', $pair->getKey()->getText());
    $this->assertEquals('2', $pair->getValue()->getText());

    $array = $this->parseStaticExpression('["a" => 1, "b" => 2]', '\Pharborist\Types\ArrayNode');
    $elements = $array->getElements();
    $pair = $elements[0];
    $this->assertEquals('"a"', $pair->getKey()->getText());
    $this->assertEquals('1', $pair->getValue()->getText());
    $pair = $elements[1];
    $this->assertEquals('"b"', $pair->getKey()->getText());
    $this->assertEquals('2', $pair->getValue()->getText());
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
    $this->parseVariable('$a', '\Pharborist\Variables\VariableNode');

    /** @var CompoundVariableNode $compound_var */
    $compound_var = $this->parseVariable('${$a}', '\Pharborist\Variables\CompoundVariableNode');
    $this->assertEquals('$a', $compound_var->getExpression()->getText());

    /** @var ArrayLookupNode $array_lookup */
    $array_lookup = $this->parseVariable('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    $array_lookup = $this->parseVariable('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    /** @var VariableVariableNode $var_var */
    $var_var = $this->parseVariable('$$a', '\Pharborist\Variables\VariableVariableNode');
    $this->assertEquals('$a', $var_var->getVariable()->getText());

    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $this->parseVariable('MyClass::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $array_lookup = $this->parseVariable('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('MyClass::$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseVariable('static::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('static', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseVariable('$c::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseVariable('$c[0]::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c[0]', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    $class_member_lookup = $this->parseVariable('$c{0}::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c{0}', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    /** @var ObjectPropertyNode $obj_property_lookup */
    $obj_property_lookup = $this->parseVariable('$o->property', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('property', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseVariable('$o->{$a}', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('{$a}', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseVariable('$o->$a', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('$a', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseVariable('$o->$$a', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('$$a', $obj_property_lookup->getProperty()->getText());
    $var_var = $obj_property_lookup->getProperty();
    $this->assertEquals('$a', $var_var->getVariable()->getText());

    /** @var CallbackCallNode $callback_call */
    $callback_call = $this->parseVariable('$a($x, $y)', '\Pharborist\Functions\CallbackCallNode');
    $this->assertEquals('$a', $callback_call->getCallback()->getText());
    $arguments = $callback_call->getArguments();
    $this->assertCount(2, $arguments);
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseVariable('$o->$a()', '\Pharborist\Objects\ObjectMethodCallNode');
    $this->assertEquals('$o', $obj_method_call->getObject()->getText());
    $this->assertEquals('$a', $obj_method_call->getMethodName()->getText());

    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseVariable('a()', '\Pharborist\Functions\FunctionCallNode');
    $this->assertEquals('a', $function_call->getName()->getText());

    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $this->parseVariable('namespace\MyClass::a()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('namespace\MyClass', $class_method_call->getClassName()->getText());
    $this->assertEquals('a', $class_method_call->getMethodName()->getText());

    $class_method_call = $this->parseVariable('MyNamespace\MyClass::$a()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('MyNamespace\MyClass', $class_method_call->getClassName());
    $this->assertEquals('$a', $class_method_call->getMethodName()->getText());

    $class_method_call = $this->parseVariable('MyClass::{$a}()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('MyClass', $class_method_call->getClassName()->getText());
    $this->assertEquals('{$a}', $class_method_call->getMethodName()->getText());

    $array_lookup = $this->parseVariable('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('a()', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $function_call = $array_lookup->getArray();
    $this->assertEquals('a', $function_call->getName()->getText());

    $class_method_call = $this->parseVariable('$class::${$f}()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('$class', $class_method_call->getClassName()->getText());
    $this->assertEquals('${$f}', $class_method_call->getMethodName()->getText());

    $array_lookup = $this->parseVariable('$class::${$f}[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$class::${$f}', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('$class', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('${$f}', $class_member_lookup->getMemberName()->getText());
    $compound_var = $class_member_lookup->getMemberName();
    $this->assertEquals('$f', $compound_var->getExpression()->getText());
  }

  /**
   * Test expression.
   */
  public function testExpression() {
    $this->parseExpression('$a', '\Pharborist\Variables\VariableNode');

    /** @var CompoundVariableNode $compound_var */
    $compound_var = $this->parseExpression('${$a}', '\Pharborist\Variables\CompoundVariableNode');
    $this->assertEquals('$a', $compound_var->getExpression()->getText());

    /** @var ArrayLookupNode $array_lookup */
    $array_lookup = $this->parseExpression('$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    $array_lookup = $this->parseExpression('$a{0}', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    /** @var VariableVariableNode $var_var */
    $var_var = $this->parseExpression('$$a', '\Pharborist\Variables\VariableVariableNode');
    $this->assertEquals('$a', $var_var->getVariable()->getText());

    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $this->parseExpression('MyClass::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $array_lookup = $this->parseExpression('MyClass::$a[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('MyClass::$a', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseExpression('static::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('static', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseExpression('$c::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());

    $class_member_lookup = $this->parseExpression('$c[0]::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c[0]', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    $class_member_lookup = $this->parseExpression('$c{0}::$a', '\Pharborist\Objects\ClassMemberLookupNode');
    $this->assertEquals('$c{0}', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $array_lookup = $class_member_lookup->getClassName();
    $this->assertEquals('$c', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());

    /** @var ClassConstantLookupNode $class_constant_lookup */
    $class_constant_lookup = $this->parseExpression('MyNamespace\MyClass::MY_CONST', '\Pharborist\Objects\ClassConstantLookupNode');
    $this->assertEquals('MyNamespace\MyClass', $class_constant_lookup->getClassName()->getText());
    $this->assertEquals('MY_CONST', $class_constant_lookup->getConstantName()->getText());

    $class_constant_lookup = $this->parseExpression('static::MY_CONST', '\Pharborist\Objects\ClassConstantLookupNode');
    $this->assertEquals('static', $class_constant_lookup->getClassName()->getText());
    $this->assertEquals('MY_CONST', $class_constant_lookup->getConstantName()->getText());

    $class_constant_lookup = $this->parseExpression('MyClass::class', '\Pharborist\Objects\ClassNameScalarNode');
    $this->assertEquals('MyClass', $class_constant_lookup->getClassName());

    $class_constant_lookup = $this->parseExpression('static::class', '\Pharborist\Objects\ClassNameScalarNode');
    $this->assertEquals('static', $class_constant_lookup->getClassName()->getText());

    /** @var ObjectPropertyNode $obj_property_lookup */
    $obj_property_lookup = $this->parseExpression('$o->property', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('property', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseExpression('$o->{$a}', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('{$a}', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseExpression('$o->$a', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('$a', $obj_property_lookup->getProperty()->getText());

    $obj_property_lookup = $this->parseExpression('$o->$$a', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$o', $obj_property_lookup->getObject()->getText());
    $this->assertEquals('$$a', $obj_property_lookup->getProperty()->getText());
    $var_var = $obj_property_lookup->getProperty();
    $this->assertEquals('$a', $var_var->getVariable()->getText());

    /** @var CallbackCallNode $callback_call */
    $callback_call = $this->parseExpression('$a()', '\Pharborist\Functions\CallbackCallNode');
    $this->assertEquals('$a', $callback_call->getCallback()->getText());

    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseExpression('$o->$a()', '\Pharborist\Objects\ObjectMethodCallNode');
    $this->assertEquals('$o', $obj_method_call->getObject()->getText());
    $this->assertEquals('$a', $obj_method_call->getMethodName()->getText());

    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseExpression('a()', '\Pharborist\Functions\FunctionCallNode');
    $this->assertEquals('a', $function_call->getName()->getText());

    /** @var ClassMethodCallNode $class_method_call */
    $class_method_call = $this->parseExpression('namespace\MyClass::a()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('namespace\MyClass', $class_method_call->getClassName()->getText());
    $this->assertEquals('a', $class_method_call->getMethodName()->getText());

    $class_method_call = $this->parseExpression('MyNamespace\MyClass::$a()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('MyNamespace\MyClass', $class_method_call->getClassName());
    $this->assertEquals('$a', $class_method_call->getMethodName()->getText());

    $class_method_call = $this->parseExpression('MyClass::{$a}()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('MyClass', $class_method_call->getClassName()->getText());
    $this->assertEquals('{$a}', $class_method_call->getMethodName()->getText());

    $array_lookup = $this->parseExpression('a()[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('a()', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $function_call = $array_lookup->getArray();
    $this->assertEquals('a', $function_call->getName());

    $class_method_call = $this->parseExpression('$class::${$f}()', '\Pharborist\Objects\ClassMethodCallNode');
    $this->assertEquals('$class', $class_method_call->getClassName()->getText());
    $this->assertEquals('${$f}', $class_method_call->getMethodName()->getText());

    $array_lookup = $this->parseExpression('$class::${$f}[0]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('$class::${$f}', $array_lookup->getArray()->getText());
    $this->assertEquals('0', $array_lookup->getKey()->getText());
    $class_member_lookup = $array_lookup->getArray();
    $this->assertEquals('$class', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('${$f}', $class_member_lookup->getMemberName()->getText());
    $compound_var = $class_member_lookup->getMemberName();
    $this->assertEquals('$f', $compound_var->getExpression()->getText());

    /** @var BinaryOperationNode $binary_op */
    $binary_op = $this->parseExpression('$a = $b++', '\Pharborist\Operators\AssignNode');
    $this->assertEquals('$a', $binary_op->getLeftOperand()->getText());
    $this->assertEquals('=', $binary_op->getOperator()->getText());
    $this->assertEquals('$b++', $binary_op->getRightOperand()->getText());

    $this->parseExpression('$a = $b ?: $c', '\Pharborist\Operators\AssignNode');

    /** @var TernaryOperationNode $ternary_node */
    $ternary_node = $this->parseExpression('$a ? $b : $c ? $d : $e', '\Pharborist\Operators\TernaryOperationNode');
    $this->assertEquals('$a ? $b : $c', $ternary_node->getCondition()->getText());
    $this->assertEquals('$d', $ternary_node->getThen()->getText());
    $this->assertEquals('$e', $ternary_node->getElse()->getText());

    $binary_op = $this->parseExpression('$a = &$b', '\Pharborist\Operators\AssignReferenceNode');
    $this->assertEquals('$a', $binary_op->getLeftOperand()->getText());
    $this->assertEquals('$b', $binary_op->getRightOperand()->getText());

    $this->parseExpression('$a or $b', '\Pharborist\Operators\LogicalOrNode');
    $this->parseExpression('$a xor $b', '\Pharborist\Operators\LogicalXorNode');
    $this->parseExpression('$a and $b', '\Pharborist\Operators\LogicalAndNode');
    $this->parseExpression('$a = $b', '\Pharborist\Operators\AssignNode');
    $this->parseExpression('$a += $b', '\Pharborist\Operators\AddAssignNode');
    $this->parseExpression('$a .= $b', '\Pharborist\Operators\ConcatAssignNode');
    $this->parseExpression('$a /= $b', '\Pharborist\Operators\DivideAssignNode');
    $this->parseExpression('$a -= $b', '\Pharborist\Operators\SubtractAssignNode');
    $this->parseExpression('$a %= $b', '\Pharborist\Operators\ModulusAssignNode');
    $this->parseExpression('$a *= $b', '\Pharborist\Operators\MultiplyAssignNode');
    $this->parseExpression('$a &= $b', '\Pharborist\Operators\BitwiseAndAssignNode');
    $this->parseExpression('$a <<= $b', '\Pharborist\Operators\BitwiseShiftLeftAssignNode');
    $this->parseExpression('$a >>= $b', '\Pharborist\Operators\BitwiseShiftRightAssignNode');
    $this->parseExpression('$a ^= $b', '\Pharborist\Operators\BitwiseXorAssignNode');
    $this->parseExpression('$a || $b', '\Pharborist\Operators\BooleanOrNode');
    $this->parseExpression('$a && $b', '\Pharborist\Operators\BooleanAndNode');
    $this->parseExpression('$a | $b', '\Pharborist\Operators\BitwiseOrNode');
    $this->parseExpression('$a & $b', '\Pharborist\Operators\BitwiseAndNode');
    $this->parseExpression('$a ^ $b', '\Pharborist\Operators\BitwiseXorNode');
    $this->parseExpression('$a == $b', '\Pharborist\Operators\EqualNode');
    $this->parseExpression('$a === $b', '\Pharborist\Operators\IdenticalNode');
    $this->parseExpression('$a != $b', '\Pharborist\Operators\NotEqualNode');
    $this->parseExpression('$a !== $b', '\Pharborist\Operators\NotIdenticalNode');
    $this->parseExpression('$a < $b', '\Pharborist\Operators\LessThanNode');
    $this->parseExpression('$a <= $b', '\Pharborist\Operators\LessThanOrEqualToNode');
    $this->parseExpression('$a >= $b', '\Pharborist\Operators\GreaterThanOrEqualToNode');
    $this->parseExpression('$a > $b', '\Pharborist\Operators\GreaterThanNode');
    $this->parseExpression('$a << $b', '\Pharborist\Operators\BitwiseShiftLeftNode');
    $this->parseExpression('$a >> $b', '\Pharborist\Operators\BitwiseShiftRightNode');
    $this->parseExpression('$a + $b', '\Pharborist\Operators\AddNode');
    $this->parseExpression('$a - $b', '\Pharborist\Operators\SubtractNode');
    $this->parseExpression('$a / $b', '\Pharborist\Operators\DivideNode');
    $this->parseExpression('$a * $b', '\Pharborist\Operators\MultiplyNode');
    $this->parseExpression('$a % $b', '\Pharborist\Operators\ModulusNode');
    $this->parseExpression('!$a', '\Pharborist\Operators\BooleanNotNode');
    $this->parseExpression('$a instanceof $b', '\Pharborist\Operators\InstanceOfNode');
    $this->parseExpression('@func()', '\Pharborist\Operators\SuppressWarningNode');
    $this->parseExpression('~$a', '\Pharborist\Operators\BitwiseNotNode');
    $this->parseExpression('clone $a', '\Pharborist\Operators\CloneNode');
    $this->parseExpression('print $a', '\Pharborist\Operators\PrintNode');
    $this->parseExpression('(array) $a', '\Pharborist\Operators\ArrayCastNode');
    $this->parseExpression('(object) $a', '\Pharborist\Operators\ObjectCastNode');
    $this->parseExpression('(bool) $a', '\Pharborist\Operators\BooleanCastNode');
    $this->parseExpression('(int) $a', '\Pharborist\Operators\IntegerCastNode');
    $this->parseExpression('(float) $a', '\Pharborist\Operators\FloatCastNode');
    $this->parseExpression('(unset) $a', '\Pharborist\Operators\UnsetCastNode');
    $this->parseExpression('(string) $a', '\Pharborist\Operators\StringCastNode');
    $this->parseExpression('--$a', '\Pharborist\Operators\PreDecrementNode');
    $this->parseExpression('++$a', '\Pharborist\Operators\PreIncrementNode');
    $this->parseExpression('$a--', '\Pharborist\Operators\PostDecrementNode');
    $this->parseExpression('$a++', '\Pharborist\Operators\PostIncrementNode');
    $this->parseExpression('+$a', '\Pharborist\Operators\PlusNode');
    $this->parseExpression('-$a', '\Pharborist\Operators\NegateNode');
  }

  /**
   * Test new expression.
   */
  public function testNew() {
    /** @var NewNode $new */
    $new = $this->parseExpression('new MyClass($x, $y)', '\Pharborist\Objects\NewNode');
    $this->assertEquals('MyClass', $new->getClassName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new static($x, $y)', '\Pharborist\Objects\NewNode');
    $this->assertEquals('static', $new->getClassName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new MyClass::$a($x, $y)', '\Pharborist\Objects\NewNode');
    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $new->getClassName();
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberLookupNode', $class_member_lookup);
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new static::$a($x, $y)', '\Pharborist\Objects\NewNode');
    $class_member_lookup = $new->getClassName();
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberLookupNode', $class_member_lookup);
    $this->assertEquals('static', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$a', $class_member_lookup->getMemberName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new MyClass::$$a($x, $y)', '\Pharborist\Objects\NewNode');
    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $new->getClassName();
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberLookupNode', $class_member_lookup);
    $this->assertEquals('MyClass', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$$a', $class_member_lookup->getMemberName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new static::$$a($x, $y)', '\Pharborist\Objects\NewNode');
    $class_member_lookup = $new->getClassName();
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberLookupNode', $class_member_lookup);
    $this->assertEquals('static', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$$a', $class_member_lookup->getMemberName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new $a::$b->c($x, $y)', '\Pharborist\Objects\NewNode');
    $this->assertEquals('$a::$b->c', $new->getClassName()->getText());
    /** @var ObjectPropertyNode $obj_property */
    $obj_property = $new->getClassName();
    $this->assertEquals('$a::$b', $obj_property->getObject()->getText());
    $this->assertEquals('c', $obj_property->getProperty()->getText());
    /** @var ClassMemberLookupNode $class_member_lookup */
    $class_member_lookup = $obj_property->getObject();
    $this->assertEquals('$a', $class_member_lookup->getClassName()->getText());
    $this->assertEquals('$b', $class_member_lookup->getMemberName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());

    $new = $this->parseExpression('new $$c($x, $y)', '\Pharborist\Objects\NewNode');
    $this->assertEquals('$$c', $new->getClassName()->getText());
    $arguments = $new->getArguments();
    $this->assertEquals('$x', $arguments[0]->getText());
    $this->assertEquals('$y', $arguments[1]->getText());
  }

  /**
   * Test invalid comparison expression.
   * @expectedException \Pharborist\ParserException
   * @expectedExceptionMessage Non-associative operators of equal precedence can not be next to each other!
   */
  public function testInvalidComparison() {
    $this->parseExpression('1 <= 1 == 2 >= 2 == 2', '\Pharborist\Operators\EqualNode');
  }

  /**
   * Test operator precedence.
   */
  public function testPrecedence() {
    $this->parseExpression('4 + 2 * 3', '\Pharborist\Operators\AddNode');
  }

  /**
   * Test valid comparison expression of different precedence.
   */
  public function testComparison() {
    $this->parseExpression('1 <= 1 == 1', '\Pharborist\Operators\EqualNode');
  }

  /**
   * Test function call.
   */
  public function testFunctionCall() {
    /** @var FunctionCallNode $function_call */
    $function_call = $this->parseExpression('do_something(&$a, $b)', '\Pharborist\Functions\FunctionCallNode');
    $this->assertEquals('do_something', $function_call->getName()->getText());
    $arguments = $function_call->getArguments();
    $this->assertEquals('&$a', $arguments[0]->getText());
    $this->assertEquals('$b', $arguments[1]->getText());
  }

  /**
   * Test static variable list.
   */
  public function testStaticVariableList() {
    /** @var StaticVariableStatementNode $static_var_stmt */
    $static_var_stmt = $this->parseSnippet('/** static vars */ static $a, $b = 1;', '\Pharborist\Variables\StaticVariableStatementNode');
    $this->assertEquals('/** static vars */', $static_var_stmt->getDocComment()->getText());
    $static_vars = $static_var_stmt->getVariables();
    $this->assertEquals('$a', $static_vars[0]->getText());
    $this->assertEquals('$b', $static_vars[1]->getName()->getText());
    $this->assertEquals('1', $static_vars[1]->getInitialValue()->getText());
  }

  /**
   * Test (new expr) expression.
   */
  public function testParenNewExpression() {
    /** @var ObjectMethodCallNode $obj_method_call */
    $obj_method_call = $this->parseExpression('(new $class($a, $b))->$method()', '\Pharborist\Objects\ObjectMethodCallNode');
    $this->assertEquals('(new $class($a, $b))', $obj_method_call->getObject()->getText());
    $this->assertEquals('$method', $obj_method_call->getMethodName()->getText());
  }

  /**
   * Test anonymous function.
   */
  public function testAnonymousFunction() {
    /** @var AnonymousFunctionNode $function */
    $function = $this->parseExpression('function(){ body(); }', '\Pharborist\Functions\AnonymousFunctionNode');
    $this->assertNull($function->getReference());
    $this->assertCount(0, $function->getParameters());
    $this->assertEquals('{ body(); }', $function->getBody()->getText());

    $function->setReference(TRUE);
    $this->assertEquals('&', $function->getReference()->getText());


    $function = $this->parseExpression('function &(){ body(); }', '\Pharborist\Functions\AnonymousFunctionNode');
    $this->assertCount(0, $function->getParameters());
    $this->assertEquals('&', $function->getReference()->getText());
    $this->assertEquals('{ body(); }', $function->getBody()->getText());

    $function = $this->parseExpression('static function(){}', '\Pharborist\Functions\AnonymousFunctionNode');
    $this->assertCount(0, $function->getParameters());

    $function = $this->parseExpression('function($a, $b) use ($x, &$y) { }', '\Pharborist\Functions\AnonymousFunctionNode');
    $parameters = $function->getParameters();
    $this->assertCount(2, $parameters);
    $this->assertEquals('$a', $parameters[0]->getText());
    $this->assertEquals('$b', $parameters[1]->getText());
    $lexical_vars = $function->getLexicalVariables();
    $this->assertCount(2, $lexical_vars);
    $this->assertEquals('$x', $lexical_vars[0]->getText());
    $this->assertEquals('&$y', $lexical_vars[1]->getText());

    /** @var \Pharborist\Operators\AssignNode $assign */
    $assign = $this->parseExpression('$f = function($a, $b) use ($x, &$y) { }', '\Pharborist\Operators\AssignNode');
    $this->assertEquals('$f', $assign->getLeftOperand()->getText());
    $this->assertInstanceOf('\Pharborist\Functions\AnonymousFunctionNode', $assign->getRightOperand());
    $function = $assign->getRightOperand();
    $parameters = $function->getParameters();
    $this->assertCount(2, $parameters);
    $this->assertEquals('$a', $parameters[0]->getText());
    $this->assertEquals('$b', $parameters[1]->getText());
    $lexical_vars = $function->getLexicalVariables();
    $this->assertCount(2, $lexical_vars);
    $this->assertEquals('$x', $lexical_vars[0]->getText());
    $this->assertEquals('&$y', $lexical_vars[1]->getText());
  }

  /**
   * Test iteration of tokens.
   */
  public function testTokenIteration() {
    /** @var \Pharborist\ExpressionStatementNode $tree */
    $tree = $this->parseSource('<?php 1 + 2;');
    $openTag = $tree->firstToken();
    $this->assertNull($openTag->previousToken());
    $one = $openTag->nextToken();
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
    $empty_statement = $this->parseSnippet('; /** function */ function test() { }', '\Pharborist\BlankStatementNode');
    /** @var FunctionDeclarationNode $function */
    $function = $empty_statement->next()->next();
    $this->assertInstanceOf('\Pharborist\Functions\FunctionDeclarationNode', $function);
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
    $this->assertEquals($source, $tree->getText());
    /** @var TemplateNode[] $templates */
    $templates = $tree->find(Filter::isInstanceOf('\Pharborist\TemplateNode'));
    $template = $templates[0];
    $this->assertEquals(5, $template->childCount());
    /** @var EchoTagStatementNode $echo_tag */
    $echo_tag = $template->firstChild()->next();
    $this->assertInstanceOf('\Pharborist\EchoTagStatementNode', $echo_tag);
    $this->assertEquals('<?=$name?>', $echo_tag->getText());
    $expressions = $echo_tag->getExpressions();
    $this->assertEquals('$name', $expressions[0]->getText());
    $template = $templates[1];
    $this->assertEquals('?><h1>End of template</h1><?php ', $template->getText());
  }

  /**
   * Tests break statement.
   */
  public function testBreak() {
    /** @var BreakStatementNode $break */
    $break = $this->parseSnippet('break;', '\Pharborist\ControlStructures\BreakStatementNode');
    $this->assertNull($break->getLevel());

    $break = $this->parseSnippet('break 1;', '\Pharborist\ControlStructures\BreakStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $break->getLevel());
    $this->assertEquals('1', $break->getLevel()->getText());

    $break = $this->parseSnippet('break(1);', '\Pharborist\ControlStructures\BreakStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $break->getLevel());
    $this->assertEquals('1', $break->getLevel()->getText());

    $break = $this->parseSnippet('break (2);', '\Pharborist\ControlStructures\BreakStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $break->getLevel());
    $this->assertEquals('2', $break->getLevel()->getText());
  }

  /**
   * Tests continue statement.
   */
  public function testContinue() {
    /** @var ContinueStatementNode $continue */
    $continue = $this->parseSnippet('continue;', '\Pharborist\ControlStructures\ContinueStatementNode');
    $this->assertNull($continue->getLevel());

    $continue = $this->parseSnippet('continue 1;', '\Pharborist\ControlStructures\ContinueStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $continue->getLevel());
    $this->assertEquals('1', $continue->getLevel()->getText());

    $continue = $this->parseSnippet('continue(1);', '\Pharborist\ControlStructures\ContinueStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $continue->getLevel());
    $this->assertEquals('1', $continue->getLevel()->getText());

    $continue = $this->parseSnippet('continue (2);', '\Pharborist\ControlStructures\ContinueStatementNode');
    $this->assertInstanceOf('\Pharborist\Types\IntegerNode', $continue->getLevel());
    $this->assertEquals('2', $continue->getLevel()->getText());
  }

  /**
   * Test global statement.
   */
  public function testGlobal() {
    $snippet = <<<'EOF'
global $a, $$b, ${expr()};
EOF;
    /** @var GlobalStatementNode $global_statement */
    $global_statement = $this->parseSnippet($snippet, '\Pharborist\Variables\GlobalStatementNode');
    $variables = $global_statement->getVariables();
    $this->assertEquals('$a', $variables[0]->getText());
    $this->assertEquals('$$b', $variables[1]->getText());
    $this->assertEquals('${expr()}', $variables[2]->getText());
  }

  /**
   * Test echo statement.
   */
  public function testEcho() {
    /** @var EchoStatementNode $echo */
    $echo = $this->parseSnippet('echo $a, expr(), PHP_EOL;', '\Pharborist\EchoStatementNode');
    $expressions = $echo->getExpressions();
    $this->assertEquals('$a', $expressions[0]->getText());
    $this->assertEquals('expr()', $expressions[1]->getText());
    $this->assertEquals('PHP_EOL', $expressions[2]->getText());
  }

  /**
   * Test goto.
   */
  public function testGoto() {
    $source = <<<'EOF'
<?php
loop:
  goto loop;
EOF;
    $tree = $this->parseSource($source);
    /** @var GotoLabelNode $goto_label */
    $goto_label = $tree->firstChild()->next();
    $this->assertInstanceOf('\Pharborist\ControlStructures\GotoLabelNode', $goto_label);
    $this->assertEquals('loop', $goto_label->getLabel()->getText());
    /** @var GotoStatementNode $goto_statement */
    $goto_statement = $tree->lastChild();
    $this->assertInstanceOf('\Pharborist\ControlStructures\GotoStatementNode', $goto_statement);
    $this->assertEquals('loop', $goto_statement->getLabel()->getText());
  }

  /**
   * Test return statement.
   */
  public function testReturn() {
    /** @var ReturnStatementNode $return_statement */
    $return_statement = $this->parseSnippet('return;', '\Pharborist\ControlStructures\ReturnStatementNode');
    $this->assertNull($return_statement->getExpression());

    $return_statement = $this->parseSnippet('return $done;', '\Pharborist\ControlStructures\ReturnStatementNode');
    $this->assertEquals('$done', $return_statement->getExpression());
  }

  /**
   * Test list.
   */
  public function testList() {
    /** @var \Pharborist\Operators\AssignNode $assign */
    $assign = $this->parseExpression('list($a, $b, list($c1, $c2)) = [1, 2, [3.1, 3.2]]', '\Pharborist\Operators\AssignNode');
    /** @var ListNode $list */
    $list = $assign->getLeftOperand();
    $this->assertInstanceOf('\Pharborist\Functions\ListNode', $list);
    $arguments = $list->getArguments();
    $this->assertCount(3, $arguments);
    $this->assertEquals('$a', $arguments[0]->getText());
    $this->assertEquals('$b', $arguments[1]->getText());
    $list = $arguments[2];
    $this->assertInstanceOf('\Pharborist\Functions\ListNode', $list);
    $arguments = $list->getArguments();
    $this->assertCount(2, $arguments);
    $this->assertEquals('$c1', $arguments[0]->getText());
    $this->assertEquals('$c2', $arguments[1]->getText());

    $assign = $this->parseExpression('list() = [1, 2]', '\Pharborist\Operators\AssignNode');
    /** @var ListNode $list */
    $list = $assign->getLeftOperand();
    $this->assertInstanceOf('\Pharborist\Functions\ListNode', $list);
    $arguments = $list->getArguments();
    $this->assertCount(0, $arguments);
  }

  /**
   * Test throw statement.
   */
  public function testThrow() {
    /** @var ThrowStatementNode $throw */
    $throw = $this->parseSnippet('throw $e;', '\Pharborist\Exceptions\ThrowStatementNode');
    $this->assertEquals('$e', $throw->getExpression()->getText());
  }

  /**
   * Test includes.
   */
  public function testIncludes() {
    /** @var ImportNode $import */
    $import = $this->parseExpression('include expr()', '\Pharborist\ControlStructures\IncludeNode');
    $this->assertEquals('expr()', $import->getExpression()->getText());

    $import = $this->parseExpression('include_once expr()', '\Pharborist\ControlStructures\IncludeOnceNode');
    $this->assertEquals('expr()', $import->getExpression()->getText());

    $import = $this->parseExpression('require expr()', '\Pharborist\ControlStructures\RequireNode');
    $this->assertEquals('expr()', $import->getExpression()->getText());

    $import = $this->parseExpression('require_once expr()', '\Pharborist\ControlStructures\RequireOnceNode');
    $this->assertEquals('expr()', $import->getExpression()->getText());
  }

  /**
   * Test isset.
   */
  public function testIsset() {
    /** @var IssetNode $isset */
    $isset = $this->parseExpression('isset($a, $b)', '\Pharborist\Functions\IssetNode');
    $arguments = $isset->getArguments();
    $this->assertEquals('$a', $arguments[0]->getText());
    $this->assertEquals('$b', $arguments[1]->getText());
  }

  /**
   * Test eval.
   */
  public function testEval() {
    /** @var EvalNode $eval */
    $eval = $this->parseExpression('eval($a)', '\Pharborist\Functions\EvalNode');
    $arguments = $eval->getArguments();
    $this->assertEquals('$a', $arguments[0]->getText());
  }

  /**
   * Test empty.
   */
  public function testEmpty() {
    /** @var EmptyNode $empty */
    $empty = $this->parseExpression('empty(expr())', '\Pharborist\Functions\EmptyNode');
    $arguments = $empty->getArguments();
    $this->assertEquals('expr()', $arguments[0]->getText());
  }

  /**
   * Test exit.
   */
  public function testExit() {
    /** @var ExitNode $exit */
    $exit = $this->parseExpression('exit', '\Pharborist\ControlStructures\ExitNode');
    $this->assertNull($exit->getExpression());

    $exit = $this->parseExpression('exit()', '\Pharborist\ControlStructures\ExitNode');
    $this->assertNull($exit->getExpression());

    $exit = $this->parseExpression('exit($status)', '\Pharborist\ControlStructures\ExitNode');
    $this->assertEquals('$status', $exit->getExpression()->getText());
  }

  /**
   * Test define.
   */
  public function testDefine() {
    $snippet = <<<'EOF'
/** Constant defined with define. */
define('TEST_CONST', 'test');
EOF;
    /** @var ExpressionStatementNode $statement */
    $statement = $this->parseSnippet($snippet, '\Pharborist\ExpressionStatementNode');
    $this->assertEquals('/** Constant defined with define. */', $statement->getDocComment()->getText());
    /** @var DefineNode $define */
    $define = $statement->getExpression();
    $this->assertInstanceOf('\Pharborist\Functions\DefineNode', $define);
    $arguments = $define->getArguments();
    $this->assertEquals("'TEST_CONST'", $arguments[0]->getText());
    $this->assertEquals("'test'", $arguments[1]->getText());
  }

  /**
   * Test complex string.
   */
  public function testComplexString() {
    $this->parseExpression('"start $a {$a} ${a} $a[0] ${a[0]} {$a[0]} ${$a} $a->b end"', '\Pharborist\Types\InterpolatedStringNode');
  }

  /**
   * Test whitespace parsing.
   */
  public function testWhitespace() {
    /** @var WhitespaceNode $ws */
    $ws = $this->parseSnippet("\n\n", '\Pharborist\WhitespaceNode');
    $this->assertEquals(2, $ws->getNewlineCount());
  }

  /**
   * Test comment parsing.
   */
  public function testComment() {
    /** @var CommentNode $comment */
    $comment = $this->parseSnippet('// test', '\Pharborist\CommentNode');
    $this->assertEquals('test', $comment->getCommentText());

    $comment = $this->parseSnippet('# test', '\Pharborist\CommentNode');
    $this->assertEquals('test', $comment->getCommentText());

    $comment = $this->parseSnippet('/* test */', '\Pharborist\CommentNode');
    $this->assertEquals('test', $comment->getCommentText());
  }

  /**
   * Test doc comment parsing.
   */
  public function testDocComment() {
    /** @var DocCommentNode $comment */
    $comment = $this->parseSnippet('/** @var string $test */', '\Pharborist\DocCommentNode');
    $this->assertEquals('@var string $test', $comment->getCommentText());

    $source = <<<'EOF'
<?php
/**
 * Line one

 * Line two
 */
some_func(); // func call
             // comment
EOF;
    $tree = $this->parseSource($source);
    $comments = $tree->find(Filter::isComment());
    $this->assertCount(2, $comments);
    $comment = $comments[0];
    $this->assertInstanceOf('\Pharborist\DocCommentNode', $comment);
    $this->assertEquals("Line one\nLine two", $comment->getCommentText());
  }

  /**
   * Test block comment.
   */
  public function testBlockComment() {
    $source = <<<'EOF'
<?php
/** ignore */

// Line one
  // Line two
// Line three

// Line four
EOF;
    $tree = $this->parseSource($source);
    $comments = $tree->children(Filter::isComment(FALSE));

    /** @var LineCommentBlockNode $comment_block */
    $comment_block = $comments[0];
    $this->assertInstanceOf('\Pharborist\LineCommentBlockNode', $comment_block);
    $this->assertEquals("Line one\nLine two\nLine three", $comment_block->getCommentText());

    /** @var CommentNode $comment */
    $comment = $comments[1];
    $this->assertInstanceOf('\Pharborist\CommentNode', $comment);
    $this->assertEquals("Line four", $comment->getCommentText());
  }

  /**
   * Test implicit semicolon.
   */
  public function testImplicitSemicolon() {
    $source = <<<'EOF'
<?php
echo 'hello' ?>
EOF;
    $tree = $this->parseSource($source);
    $this->assertInstanceOf('\Pharborist\EchoStatementNode', $tree->firstChild()->next());
  }

  /**
   * Test constants.
   */
  public function testConstants() {
    $this->parseExpression('SOME_CONST', '\Pharborist\Constants\ConstantNode');

    /** @var TrueNode $true */
    $this->parseExpression('true', '\Pharborist\Types\TrueNode');
    $true = $this->parseExpression('TRUE', '\Pharborist\Types\TrueNode');
    $this->assertTrue($true->toValue());

    /** @var FalseNode $false */
    $this->parseExpression('false', '\Pharborist\Types\FalseNode');
    $false = $this->parseExpression('FALSE', '\Pharborist\Types\FalseNode');
    $this->assertFalse($false->toValue());

    $this->parseExpression('NULL', '\Pharborist\Types\NullNode');
    $this->parseExpression('null', '\Pharborist\Types\NullNode');
  }

  /**
   * @requires PHP 5.5
   */
  public function testFinally() {
    $snippet = <<<'EOF'
try { try_body(); }
finally { finally_body(); }
EOF;
    /** @var TryCatchNode $try_catch */
    $try_catch = $this->parseSnippet($snippet, '\Pharborist\Exceptions\TryCatchNode');
    $this->assertEquals('{ try_body(); }', $try_catch->getTry()->getText());
    $this->assertNotNull($try_catch->getFinally());
    $this->assertEquals('{ finally_body(); }', $try_catch->getFinally()->getText());
  }

  /**
   * @requires PHP 5.6
   */
  public function testPower() {
    $this->parseStaticExpression('1 ** 2', '\Pharborist\Operators\PowerNode');
    $this->parseExpression('1 ** 2', '\Pharborist\Operators\PowerNode');
    $this->parseExpression('1 **= 2', '\Pharborist\Operators\PowerAssignNode');
  }

  /**
   * Test combining array lookup with object property lookup.
   */
  public function testObjectPropertyArray() {
    /** @var ArrayLookupNode $lookup */
    $lookup = $this->parseExpression('$account->pants_status[LANGUAGE_NONE]', '\Pharborist\ArrayLookupNode');
    $this->assertEquals('LANGUAGE_NONE', $lookup->getKey()->getText());
    /** @var ObjectPropertyNode $property */
    $property = $lookup->getArray();
    $this->assertInstanceOf('\Pharborist\Objects\ObjectPropertyNode', $property);
    $this->assertSame($lookup, $property->parent());
    $this->assertEquals('$account', $property->getObject()->getText());
    $this->assertEquals('pants_status', $property->getProperty()->getText());

    $property = $this->parseExpression('$account->$a[KEY]', '\Pharborist\Objects\ObjectPropertyNode');
    $this->assertEquals('$account', $property->getObject()->getText());
    $lookup = $property->getProperty();
    $this->assertInstanceOf('\Pharborist\ArrayLookupNode', $lookup);
    $this->assertEquals('$a', $lookup->getArray()->getText());
    $this->assertEquals('KEY', $lookup->getKey()->getText());
  }

  /**
   * Test calling a callback function that is inside an array property.
   */
  public function testObjectPropertyArrayCall() {
    /** @var CallbackCallNode $call */
    $call = $this->parseExpression('$object->property[KEY]()', '\Pharborist\Functions\CallbackCallNode');
    /** @var ArrayLookupNode $lookup */
    $lookup = $call->getCallback();
    $this->assertInstanceOf('\Pharborist\ArrayLookupNode', $lookup);
    /** @var ObjectPropertyNode $property */
    $property = $lookup->getArray();
    $this->assertInstanceOf('\Pharborist\Objects\ObjectPropertyNode', $property);
    $this->assertEquals('$object', $property->getObject()->getText());
    $this->assertEquals('property', $property->getProperty()->getText());
  }

  /**
   * Test calling a method where method name is dynamic.
   */
  public function testObjectDynamicMethodCall() {
    /** @var ObjectMethodCallNode $method_call */
    $method_call = $this->parseExpression('$object->$a[KEY]()', '\Pharborist\Objects\ObjectMethodCallNode');
    $this->assertEquals('$object', $method_call->getObject()->getText());
    /** @var ArrayLookupNode $lookup */
    $lookup = $method_call->getMethodName();
    $this->assertInstanceOf('\Pharborist\ArrayLookupNode', $lookup);
    $this->assertEquals('$a', $lookup->getArray()->getText());
    $this->assertEquals('KEY', $lookup->getKey()->getText());
  }
}
