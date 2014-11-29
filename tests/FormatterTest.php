<?php
namespace Pharborist;

class FormatterTest extends \PHPUnit_Framework_TestCase {
  protected function formatSnippet($snippet) {
    /** @var ParentNode $node */
    $node = Parser::parseSnippet($snippet);
    $formatter = new Formatter();
    $node->acceptVisitor($formatter);
    return $node->getText();
  }

  public function testBinaryOp() {
    $snippet = '$a=1+2;';
    $actual = $this->formatSnippet($snippet);
    $expected = '$a = 1 + 2;';
    $this->assertEquals($expected, $actual);
  }

  public function testIf() {
    $snippet = 'if( $a ) test(); elseif ($b ) test(); elseif ($c){test();}else test();';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
if ($a) {
  test();
}
elseif ($b) {
  test();
}
elseif ($c) {
  test();
}
else {
  test();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testAlternativeIf() {
    $snippet = 'if( $a ) : test(); elseif ($b ): test(); elseif ($c):test();else: test();endif;';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
if ($a):
  test();
elseif ($b):
  test();
elseif ($c):
  test();
else:
  test();
endif;
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testNestedIf() {
    $snippet = 'if( $a ) test(); elseif ($b ) test(); else if ($c){test();}else test();';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
if ($a) {
  test();
}
elseif ($b) {
  test();
}
else {
  if ($c) {
    test();
  }
  else {
    test();
  }
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testWhile() {
    $snippet = 'while($a) while($b){test();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
while ($a) {
  while ($b) {
    test();
  }
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testDoWhile() {
    $snippet = 'do {test();}while($a);';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
do {
  test();
} while ($a);
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testSwitch() {
    $snippet = <<<'EOF'
switch ($cond){case 'hello':
  case 'world':
    case '!':
    $a = 1;
    break;case 'test':
    $b = 1;
    break;
    default:
    test();
    break;
}
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
switch ($cond) {
  case 'hello':
  case 'world':
  case '!':
    $a = 1;
    break;
  case 'test':
    $b = 1;
    break;
  default:
    test();
    break;
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testArray() {
    $snippet = <<<'EOF'
$arr = array (  'hello',  'world'  ,'!'   );
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
$arr = ['hello', 'world', '!'];
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testKeyedArray() {
    $snippet = <<<'EOF'
$arr = array(
  'a' => 'apple', 'b' => 'banana',
  'o' => 'orange'
);
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
$arr = [
  'a' => 'apple',
  'b' => 'banana',
  'o' => 'orange',
];
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testLongArray() {
    $snippet = <<<'EOF'
$arr = array('blah', 'blah', 'blah', 'blah','blah', 'blah','blah', 'blah','blah', 'blah');
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
$arr = [
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
  'blah',
];
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testNestedArray() {
    $snippet = <<<'EOF'
if ($test)
$arr = array('blah', 'blah', 'blah', 'blah',array('blah', 'blah','blah'), 'blah',array('blah', 'blah', 'blah', 'blah', 'blah', 'blah'),'blah', 'blah');
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
if ($test) {
  $arr = [
    'blah',
    'blah',
    'blah',
    'blah',
    ['blah', 'blah', 'blah'],
    'blah',
    [
      'blah',
      'blah',
      'blah',
      'blah',
      'blah',
      'blah',
    ],
    'blah',
    'blah',
  ];
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testFor() {
    $snippet = 'for($i=0;$i<$n;$i++){test();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
for ($i = 0; $i < $n; $i++) {
  test();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testForeach() {
    $snippet = 'foreach($a as $k=>$v){test();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
foreach ($a as $k => $v) {
  test();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testFunctionCall() {
    $snippet = 'test ( $a,$b  );';
    $actual = $this->formatSnippet($snippet);
    $expected = 'test($a, $b);';
    $this->assertEquals($expected, $actual);
  }

  public function testFunctionCallArray() {
    $snippet = <<<'EOF'
test(
  $a,
  [$b,
  $c]
);
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
test($a, [
  $b,
  $c,
]);
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testFunction() {
    $snippet = 'function test ( $a,$b  ,  $c=1){run();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
function test($a, $b, $c = 1) {
  run();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testClass() {
    $snippet = 'class Test {private $a;protected $b;function test ( $a,$b  ,  $c=1){run();}function run(){test();}}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class Test {
  private $a;

  protected $b;

  public function test($a, $b, $c = 1) {
    run();
  }

  public function run() {
    test();
  }
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testTrait() {
    $snippet = 'trait Test {private $a;protected $b;function test ( $a,$b  ,  $c=1){run();}function run(){test();}}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
trait Test {
  private $a;

  protected $b;

  public function test($a, $b, $c = 1) {
    run();
  }

  public function run() {
    test();
  }
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testInterface() {
    $snippet = 'interface Test {function test ( $a,$b  ,  $c=1);function run();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
interface Test {
  public function test($a, $b, $c = 1);

  public function run();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testSpecialConstants() {
    $snippet = '[true, false, null];';
    $actual = $this->formatSnippet($snippet);
    $expected = '[TRUE, FALSE, NULL];';
    $this->assertEquals($expected, $actual);
  }

  public function testTryCatch() {
    $snippet = 'try{test();}catch( Exception $e ) { test();}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
try {
  test();
}
catch (Exception $e) {
  test();
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testNew() {
    $snippet = 'new Test;';
    $actual = $this->formatSnippet($snippet);
    $expected = 'new Test();';
    $this->assertEquals($expected, $actual);
  }

  public function testUnary() {
    $snippet = '$x = 4 - - 2;';
    $actual = $this->formatSnippet($snippet);
    $expected = '$x = 4 - -2;';
    $this->assertEquals($expected, $actual);
  }

  public function testCast() {
    $snippet = '(string)1;';
    $actual = $this->formatSnippet($snippet);
    $expected = '(string) 1;';
    $this->assertEquals($expected, $actual);
  }

  public function testPost() {
    $snippet = '$x ++;';
    $actual = $this->formatSnippet($snippet);
    $expected = '$x++;';
    $this->assertEquals($expected, $actual);
  }

  public function testObjectChain() {
    $snippet = <<<'EOF'
$obj
->method()
    ->method();
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
$obj
  ->method()
  ->method();
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testExpression() {
    $snippet = <<<'EOF'
$a = 1 +
 2;
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = '$a = 1 + 2;';
    $this->assertEquals($expected, $actual);
  }

  public function testMethodModifiers() {
    $snippet = <<<'EOF'
abstract class Test {
  public abstract function abstractMethod();

  public final function finalMethod() {
  }

  static public function classMethod() {
  }

  final static public function testFinalStatic() {
  }
}
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
abstract class Test {
  abstract public function abstractMethod();

  final public function finalMethod() {
  }

  public static function classMethod() {
  }

  final public static function testFinalStatic() {
  }
}
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testPsr2If() {
    $settings = Settings::getAll();
    Settings::set('formatter.else_newline', FALSE);
    $snippet = 'if( $a ) test(); elseif ($b ) test(); elseif ($c){test();}else test();';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
if ($a) {
  test();
} elseif ($b) {
  test();
} elseif ($c) {
  test();
} else {
  test();
}
EOF;
    $this->assertEquals($expected, $actual);
    Settings::setAll($settings);
  }

  public function testPsr2Class() {
    $settings = Settings::getAll();
    Settings::set('formatter.declaration_brace_newline', TRUE);
    $snippet = 'class Test{public function test(){run();}}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class Test
{
  public function test()
  {
    run();
  }
}
EOF;
    $this->assertEquals($expected, $actual);
    Settings::setAll($settings);
  }

  public function testPsr2ImplementsWrap() {
    $settings = Settings::getAll();
    Settings::set('formatter.declaration_brace_newline', TRUE);
    Settings::set('formatter.list.keep_wrap', TRUE);

    $snippet = "class Test extends ParentClass implements
    TestInterface {}";
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class Test extends ParentClass implements
  TestInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    $snippet = "class Test extends ParentClass implements OneInterface,
    TwoInterface,ThreeInterface,FourInterface,FiveInterface {}";
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class Test extends ParentClass implements
  OneInterface,
  TwoInterface,
  ThreeInterface,
  FourInterface,
  FiveInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::set('formatter.list.wrap_if_long', TRUE);
    $snippet = 'class Test extends ParentClass implements OneInterface,TwoInterface,ThreeInterface,FourInterface,FiveInterface {}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class Test extends ParentClass implements
  OneInterface,
  TwoInterface,
  ThreeInterface,
  FourInterface,
  FiveInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::setAll($settings);
  }

  public function testPsr2ExtendsWrap() {
    $settings = Settings::getAll();
    Settings::set('formatter.declaration_brace_newline', TRUE);
    Settings::set('formatter.list.keep_wrap', TRUE);

    $snippet = "interface TestInterface extends
    ParentInterface {}";
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
interface TestInterface extends
  ParentInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    $snippet = "interface TestInterface extends OneInterface,
    TwoInterface,ThreeInterface,FourInterface,FiveInterface {}";
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
interface TestInterface extends
  OneInterface,
  TwoInterface,
  ThreeInterface,
  FourInterface,
  FiveInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::set('formatter.list.wrap_if_long', TRUE);
    $snippet = 'interface TestInterface extends OneInterface,TwoInterface,ThreeInterface,FourInterface,FiveInterface {}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
interface TestInterface extends
  OneInterface,
  TwoInterface,
  ThreeInterface,
  FourInterface,
  FiveInterface
{
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::setAll($settings);
  }

  public function testPsr2FunctionDeclaration() {
    $settings = Settings::getAll();
    Settings::set('formatter.declaration_brace_newline', TRUE);
    Settings::set('formatter.list.keep_wrap', TRUE);

    $snippet = <<<'EOF'
function test(
$a,
$b) {}
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
function test(
  $a,
  $b
) {
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::set('formatter.list.wrap_if_long', TRUE);
    $snippet = 'function test($someLongParameterName, $anotherLongParameterName, $yetAnotherParameterName){}';
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
function test(
  $someLongParameterName,
  $anotherLongParameterName,
  $yetAnotherParameterName
) {
}
EOF;
    $this->assertEquals($expected, $actual);

    Settings::setAll($settings);
  }
}
