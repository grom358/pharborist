<?php
namespace Pharborist;

class ArrayLookupNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $lookup = ArrayLookupNode::create(Token::variable('$form_state'), new StringNode(T_CONSTANT_ENCAPSED_STRING, "'storage'"));
    $this->assertEquals('$form_state[\'storage\']', $lookup->getText());
  }
}
