<?php
namespace Pharborist;

class ObjectMethodCallNodeTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var \Pharborist\Objects\ObjectMethodCallNode
   */
  private $call;

  public function __construct() {
    $this->call = Parser::parseSnippet('$mulder->scully();')->firstChild();
  }

  public function testGetObject() {
    $this->assertInstanceOf('Pharborist\Variables\VariableNode', $this->call->getObject());
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

  public function testGetPreviousCall() {
    $call = Parser::parseSnippet('\Drupal::database()->insert("razmatazz");')->firstChild();
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $call);
    $this->assertInstanceOf('\Pharborist\Objects\ClassMethodCallNode', $call->getPreviousCall());

    $call = Parser::parseSnippet('$raz->matazz();')->firstChild();
    $this->assertInstanceOf('\Pharborist\Objects\ObjectMethodCallNode', $call);
    $this->assertNull($call->getPreviousCall());
  }
}
