<?php
namespace Pharborist;

use Pharborist\Types\BooleanNode;

class BooleanNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $true = BooleanNode::create(TRUE);
    $this->assertEquals('TRUE', $true->getText());

    $true = BooleanNode::create(FALSE);
    $this->assertEquals('FALSE', $true->getText());
  }

  public function testToUpper() {
    Settings::set('formatter.boolean_null.upper', FALSE);
    $true = BooleanNode::create(TRUE);
    $this->assertEquals('true', $true->getText());
    $this->assertEquals('TRUE', $true->toUpperCase()->getText());
  }

  public function testToLower() {
    Settings::set('formatter.boolean_null.upper', TRUE);
    $true = BooleanNode::create(TRUE);
    $this->assertEquals('TRUE', $true->getText());
    $this->assertEquals('true', $true->toLowerCase()->getText());
  }
}
