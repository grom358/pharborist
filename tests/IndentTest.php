<?php
namespace Pharborist;

/**
 * Test indenting of code.
 */
class IndentTest extends \PHPUnit_Framework_TestCase {
  public function testExpr() {
    $source = <<<'EOF'
<?php
echo 'hello' .
'world', PHP_EOL;
EOF;
    $expected = <<<'EOF'
<?php
echo 'hello' .
  'world', PHP_EOL;
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testWhile() {
    $source = <<<'EOF'
<?php
while ($cond) {
test();
}
EOF;
    $expected = <<<'EOF'
<?php
while ($cond) {
  test();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testDoWhile() {
    $source = <<<'EOF'
<?php
do {
test();
} while ($cond);
EOF;
    $expected = <<<'EOF'
<?php
do {
  test();
} while ($cond);
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testIf() {
    $source = <<<'EOF'
<?php
if ($cond) {
test();
}
elseif ($cond) {
    test();
    }
else {
   test();
}
EOF;
    $expected = <<<'EOF'
<?php
if ($cond) {
  test();
}
elseif ($cond) {
  test();
}
else {
  test();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testFunction() {
    $source = <<<'EOF'
<?php
function test() {
doSomething();
}
EOF;
    $expected = <<<'EOF'
<?php
function test() {
  doSomething();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testClass() {
    $source = <<<'EOF'
<?php
class Test {
public function test(
$param1,
$param2
) {
doSomething();
}
}
EOF;
    $expected = <<<'EOF'
<?php
class Test {
  public function test(
    $param1,
    $param2
  ) {
    doSomething();
  }
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testInterface() {
    $source = <<<'EOF'
<?php
interface Test {
public function test();
}
EOF;
    $expected = <<<'EOF'
<?php
interface Test {
  public function test();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testDocComment() {
    $source = <<<'EOF'
<?php
interface Test {
/**
 * Test
 */
public function test();

      /**
       * Another
       */
      public function other();
}
EOF;
    $expected = <<<'EOF'
<?php
interface Test {
  /**
   * Test
   */
  public function test();

  /**
   * Another
   */
  public function other();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testLineComment() {
    $source = <<<'EOF'
<?php
interface Test {
// Test
public function test();

     // Another
     public function other();
}
EOF;
    $expected = <<<'EOF'
<?php
interface Test {
  // Test
  public function test();

  // Another
  public function other();
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testMultilineComment() {
    $source = <<<'EOF'
<?php
interface Test {
/*
 * Test
 */
public function test();

     /*
      * Another
      */
     public function other();

     /* This is a multi line comment
yet another line of comment */
public function fix();

   /*
if ($cond) {
  test();
}
 */
}
EOF;
    $expected = <<<'EOF'
<?php
interface Test {
  /*
   * Test
   */
  public function test();

  /*
   * Another
   */
  public function other();

  /* This is a multi line comment
     yet another line of comment */
  public function fix();

  /*
if ($cond) {
  test();
}
   */
}
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }

  public function testArray() {
    $source = <<<'EOF'
<?php
$a = array(
'a',
'b' => array(
'one',
'two'
),
'c'
);
EOF;
    $expected = <<<'EOF'
<?php
$a = array(
  'a',
  'b' => array(
    'one',
    'two'
  ),
  'c'
);
EOF;
    $tree = Parser::parseSource($source);
    $tree->indent('  ');
    $this->assertEquals($expected, $tree->getText());
  }
}
