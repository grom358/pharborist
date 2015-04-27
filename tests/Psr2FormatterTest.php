<?php
namespace Pharborist;

class Psr2FormatterTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Formatter
   */
  protected $formatter;

  public function setUp() {
    $this->formatter = FormatterFactory::getPsr2Formatter();
  }

  protected function formatSnippet($snippet) {
    /** @var ParentNode $node */
    $node = Parser::parseSnippet($snippet);
    $this->formatter->format($node);
    return $node->getText();
  }

  public function testIf() {
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
  }

  public function testRootNode() {
    $snippet = <<<'END'
<?php
namespace Foo\Baz; class Blorg { public function __construct() {}}
END;
    $expected = <<<'END'
<?php
namespace Foo\Baz;

class Blorg
{
    public function __construct()
    {
    }
}
END;
    $doc = Parser::parseSource($snippet);
    $this->formatter->format($doc);
    $this->assertEquals($expected, $doc->getText());
  }

  public function testClass() {
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
  }

  public function testImplementsWrap() {
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
  }

  public function testExtendsWrap() {
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
  }

  public function testPsr2FunctionDeclaration() {
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
  }

  public function testClassComments() {
    $snippet = <<<'EOF'
class MyTest {
  /**
* Some property.
   */
  private $someProperty;
    /**
        * Some method.
 */
  public function someMethod() {
  }

}
EOF;
    $actual = $this->formatSnippet($snippet);
    $expected = <<<'EOF'
class MyTest
{
    /**
     * Some property.
     */
    private $someProperty;

    /**
     * Some method.
     */
    public function someMethod()
    {
    }
}
EOF;
    $this->assertEquals($expected, $actual);
  }
}
