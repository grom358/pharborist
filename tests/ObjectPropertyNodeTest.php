<?php
namespace Pharborist;

class ObjectPropertyNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetRootProperty() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$foo->bar->baz');
    $this->assertInstanceOf('\Pharborist\ObjectPropertyNode', $property);
    $this->assertEquals('bar', $property->getRootProperty()->getText());

    $property = Parser::parseExpression('$foo->$bar');
    $this->assertInstanceOf('\Pharborist\ObjectPropertyNode', $property);
    $this->assertEquals('$bar', $property->getRootProperty()->getText());
  }

  /**
   * @depends testRootProperty
   */
  public function testGetPropertyName() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$node->field_foo');
    $this->assertEquals('field_foo', $property->getPropertyName());

    $property = Parser::parseExpression('$node->$field["value"]');
    $this->assertNull($property->getPropertyName());
  }
}
