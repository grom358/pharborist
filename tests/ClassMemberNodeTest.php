<?php

namespace Pharborist;

class ClassMemberNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $member = ClassMemberNode::create('lancelot');
    $this->assertInstanceOf('\Pharborist\ClassMemberListNode', $member);
    $this->assertEquals('public $lancelot;', $member->getText());

    $member = ClassMemberNode::create('robin', StringNode::create("'cowardly'"), 'protected');
    $this->assertInstanceOf('\Pharborist\ClassMemberListNode', $member);
    $this->assertEquals('protected $robin = \'cowardly\';', $member->getText());
  }
}
