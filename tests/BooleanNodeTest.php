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
    $default_formatter = FormatterFactory::getDefaultFormatter();
    $formatter = new Formatter(['boolean_null_upper' => FALSE]);
    FormatterFactory::setDefaultFormatter($formatter);
    $true = BooleanNode::create(TRUE);
    $this->assertEquals('true', $true->getText());
    $this->assertEquals('TRUE', $true->toUpperCase()->getText());
    FormatterFactory::setDefaultFormatter($default_formatter);
  }

  public function testToLower() {
    $default_formatter = FormatterFactory::getDefaultFormatter();
    $formatter = new Formatter(['boolean_null_upper' => TRUE]);
    FormatterFactory::setDefaultFormatter($formatter);
    $true = BooleanNode::create(TRUE);
    $this->assertEquals('TRUE', $true->getText());
    $this->assertEquals('true', $true->toLowerCase()->getText());
    FormatterFactory::setDefaultFormatter($default_formatter);
  }
}
