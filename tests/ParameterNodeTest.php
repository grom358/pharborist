<?php
namespace Pharborist;

/**
 * Tests various methods of ParameterNode.
 */
class ParameterNodeTest extends \PHPUnit_Framework_TestCase {
  public function testParameterNode() {
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = Parser::parseSnippet('function foo(stdClass &$a = NULL) { $a = new stdClass(); }');
    $parameter = $function->getParameter(0);

    $this->assertInstanceOf('Pharborist\Functions\FunctionDeclarationNode', $parameter->getFunction());
    $this->assertEquals('stdClass', $parameter->getTypeHint()->getText());
    $this->assertInstanceOf('Pharborist\TokenNode', $parameter->getReference());
    $this->assertFalse($parameter->isRequired());
    $this->assertTrue($parameter->isOptional());
    $this->assertEquals('$a', $parameter->getVariable()->getText());
    $this->assertEquals('a', $parameter->getName());
    $this->assertEquals('NULL', $parameter->getValue()->getText());
    $parameter->setValue(NULL);
    $this->assertNull($parameter->getValue());
    $this->assertFalse($parameter->isOptional());
    $this->assertTrue($parameter->isRequired());

    $parameter->setName('b', TRUE);
    $variable_name = $function->find(Filter::isInstanceOf('Pharborist\Variables\VariableNode'))[0]->getText();
    $this->assertEquals('$b', $variable_name);
  }
}
