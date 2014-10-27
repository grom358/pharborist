<?php
namespace Pharborist;

use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Functions\ParameterNode;

/**
 * Tests various methods of ParameterTrait.
 */
class ParameterTraitTest extends \PHPUnit_Framework_TestCase {
  public function testHasParameter() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = Parser::parseSnippet('function foo(stdClass &$a = NULL) { $a = new stdClass(); }');

    $this->assertTrue($function->hasParameter('a'));
    $this->assertTrue($function->hasParameter('$a'));
    $this->assertTrue($function->hasParameter($function->getParameterAtIndex(0)));
    $this->assertFalse($function->hasParameter('b'));
    $blorf = ParameterNode::create('$blorf');
    $this->assertFalse($function->hasParameter($blorf));

    $this->assertTrue($function->hasParameter('a', 'stdClass'));
    $this->assertFalse($function->hasParameter('a', 'Node'));

    $this->assertFalse($function->hasRequiredParameter('a', 'stdClass'));
    $this->assertTrue($function->hasOptionalParameter('a', 'stdClass'));

    $function->getParameterAtIndex(0)->setValue(NULL);
    $this->assertTrue($function->hasRequiredParameter('a', 'stdClass'));
    $this->assertFalse($function->hasOptionalParameter('a', 'stdClass'));
  }

  public function testGetParameters() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = Parser::parseSnippet('function foo($a, $a = 1, $b, $c) {}');
    $parameters = $function->getParameters();
    $a = $parameters['a'];
    $this->assertEquals('$a = 1', $a->getText());
    $this->assertEquals('$a', $parameters[0]);
    $this->assertTrue(isset($parameters[2]));
    $this->assertTrue(isset($parameters['b']));
    $this->assertEquals('$b', $parameters['b']);
    $this->assertEquals('$c', $parameters[3]);
  }

  /**
   * @depends testHasParameter
   */
  public function testAppendParameter() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = Parser::parseSnippet('function baz() {}');

    $function->appendParameter(ParameterNode::create('$neo'));
    $this->assertTrue($function->hasParameter('neo'));

    $function->appendParameter(function(FunctionDeclarationNode $function) {
      return ParameterNode::create('$trinity');
    });
    $this->assertTrue($function->hasParameter('trinity'));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testHasParamterInvalidArgumentException() {
    Parser::parseSnippet('function foo($bar) {}')->hasParameter(1);
  }

  /**
   * @requires PHP 5.6
   */
  public function testIsVariadic() {
    $doc = <<<'END'
<?php
function foo($a, $b, ...$c) {
}
END;
    /** @var \Pharborist\Functions\FunctionDeclarationNode $func */
    $func = Parser::parseSource($doc)->children(Filter::isFunction('foo'))->get(0);
    $this->assertTrue($func->isVariadic());

    $doc = <<<'END'
<?php
function baz($a, $b, $c) {
}
END;
    $func = Parser::parseSource($doc)->children(Filter::isFunction('baz'))->get(0);
    $this->assertFalse($func->isVariadic());
  }
}
