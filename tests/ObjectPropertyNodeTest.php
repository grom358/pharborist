<?php
namespace Pharborist;

use Pharborist\Objects\ObjectPropertyNode;

class ObjectPropertyNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetRootProperty() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$foo->bar->baz');
    $this->assertInstanceOf('\Pharborist\Objects\ObjectPropertyNode', $property);
    $this->assertEquals('bar', $property->getRootProperty()->getText());

    $property = Parser::parseExpression('$foo->$bar');
    $this->assertInstanceOf('\Pharborist\Objects\ObjectPropertyNode', $property);
    $this->assertEquals('$bar', $property->getRootProperty()->getText());
  }

  /**
   * @depends testGetRootProperty
   */
  public function testGetPropertyName() {
    /** @var ObjectPropertyNode $property */
    $property = Parser::parseExpression('$node->field_foo');
    $this->assertEquals('field_foo', $property->getPropertyName());

    $property = Parser::parseExpression('$node->$field["value"]');
    $this->assertNull($property->getPropertyName());
  }
}
