<?php
namespace Pharborist;

class FormatterTest extends \PHPUnit_Framework_TestCase {
  protected function formatSnippet($snippet) {
    /** @var ParentNode $node */
    $node = Parser::parseSnippet($snippet);
    $formatter = new Formatter();
    $node->accept($formatter);
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
$arr = array('hello', 'world', '!');
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
$arr = array(
  'a' => 'apple',
  'b' => 'banana',
  'o' => 'orange',
);
EOF;
    $this->assertEquals($expected, $actual);
  }

  public function testLongArray() {
    $snippet = <<<'EOF'
$arr = array('blah', 'blah', 'blah', 'blah','blah', 'blah','blah', 'blah','blah', 'blah');
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
$arr = array(
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
);
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
  $arr = array(
    'blah',
    'blah',
    'blah',
    'blah',
    array('blah', 'blah', 'blah'),
    'blah',
    array(
      'blah',
      'blah',
      'blah',
      'blah',
      'blah',
      'blah',
    ),
    'blah',
    'blah',
  );
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
    $snippet = 'array(true, false, null);';
    $actual = $this->formatSnippet($snippet);
    $expected = 'array(TRUE, FALSE, NULL);';
    $this->assertEquals($expected, $actual);
  }
}
