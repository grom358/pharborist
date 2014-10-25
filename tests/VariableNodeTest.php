<?php

namespace Pharborist;

use Pharborist\Variables\VariableNode;

class VariableNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetName() {
    $var = new VariableNode(T_VARIABLE, '$form');
    $this->assertEquals('form', $var->getName());
  }

  /**
   * @depends testGetName
   */
  public function testSetName() {
    $var = new VariableNode(T_VARIABLE, '$x');
    $var->setName('$y');
    $this->assertEquals('y', $var->getName());
    $var->setName('z');
    $this->assertEquals('z', $var->getName());
  }
}
