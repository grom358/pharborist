<?php

namespace Pharborist;

class ObjectMethodCallNodeTest extends \PHPUnit_Framework_TestCase {
  private $call;

  public function __construct() {
    $this->call = Parser::parseSnippet('$mulder->scully();')->firstChild();
  }

  public function testGetObject() {
    $this->assertInstanceOf('Pharborist\VariableNode', $this->call->getObject());
    $this->assertEquals('mulder', $this->call->getObject()->getName());
  }

  public function testGetMethodName() {
    $this->assertEquals('scully', $this->call->getMethodName());
  }

  public function testSetMethodName() {
    $this->call->setMethodName('skinner');
    $this->assertEquals('skinner', $this->call->getMethodName());
    $this->assertEquals('$mulder->skinner()', $this->call->getText());
  }
}
