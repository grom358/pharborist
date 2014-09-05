<?php
namespace Pharborist;

/**
 * Tests various methods of ParameterNode.
 */
class ParameterNodeTest extends \PHPUnit_Framework_TestCase {
  public function testParameterRewrite() {
    $function = Parser::parseSnippet('function foo($a) { $a = 1; }');

    $variable_name = $function
      ->getParameter(0)
      ->setName('b', TRUE)
      ->getFunction()
      ->find(Filter::isInstanceOf('Pharborist\VariableNode'))[0]
      ->getText();
    $this->assertEquals('$b', $variable_name);
  }
}
