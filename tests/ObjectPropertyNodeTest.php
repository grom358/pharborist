<?php
namespace Pharborist;

class ObjectPropertyNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetRootProperty() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$node->field_foo[LANGUAGE_NONE][0]["value"]');
    $this->assertInstanceOf('\Pharborist\ObjectPropertyNode', $property);
    $root_property = $property->getRootProperty();
    $this->assertInstanceOf('\Pharborist\TokenNode', $root_property);
    $this->assertSame(T_STRING, $root_property->getType());
    $this->assertEquals('field_foo', $root_property->getText());

    $property = Parser::parseExpression('$node->$field["value"]');
    $this->assertInstanceOf('\Pharborist\ObjectPropertyNode', $property);
    $root_property = $property->getRootProperty();
    $this->assertInstanceOf('\Pharborist\VariableNode', $root_property);
    $this->assertEquals('$field', $root_property->getText());
  }

  /**
   * @depends testRootProperty
   */
  public function testGetPropertyName() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$node->field_foo[LANGUAGE_NONE][0]["value"]');
    $this->assertEquals('field_foo', $property->getPropertyName());

    $property = Parser::parseExpression('$node->$field["value"]');
    $this->assertNull($property->getPropertyName());
  }
}
