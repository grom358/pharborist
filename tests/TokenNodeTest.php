<?php
namespace Pharborist;

class TokenNodeTest extends \PHPUnit_Framework_TestCase {
  public function testGetTypeName() {
    $id = new TokenNode(T_STRING, 'hello');
    $this->assertEquals('T_STRING', $id->getTypeName());

    $comma = new TokenNode(',', ',');
    $this->assertEquals(',', $comma->getTypeName());
  }
}
