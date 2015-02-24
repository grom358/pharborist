<?php

namespace Pharborist;

use Pharborist\Objects\ClassMemberNode;
use Pharborist\Objects\ClassNode;
use Pharborist\Types\StringNode;

class ClassMemberNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $member = ClassMemberNode::create('lancelot');
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $member);
    $this->assertEquals('public $lancelot;', $member->getText());

    $member = ClassMemberNode::create('robin', StringNode::create("'cowardly'"), 'protected');
    $this->assertInstanceOf('\Pharborist\Objects\ClassMemberListNode', $member);
    $this->assertEquals('protected $robin = \'cowardly\';', $member->getText());
  }

  public function testStatic() {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { public $bar; }');
    /** @var ClassMemberNode $a */
    $a = $class_node->getProperties()[0];
    $this->assertFalse($a->isStatic());
    $this->assertNull($a->getStatic());
    $a->setStatic(TRUE);
    $this->assertTrue($a->isStatic());
    $this->assertSame('public static $bar;', $a->closest(Filter::isInstanceOf('\Pharborist\Objects\ClassMemberListNode'))->getText());

    $class_node = Parser::parseSnippet('class Bar { protected static $baz; }');
    /** @var ClassMemberNode $b */
    $b = $class_node->getProperties()[0];
    $this->assertTrue($b->isStatic());
    $this->assertInstanceOf('\Pharborist\TokenNode', $b->getStatic());
    $this->assertSame(T_STATIC, $b->getStatic()->getType());
    $b->setStatic(FALSE);
    $this->assertFalse($b->isStatic());
    $this->assertSame('protected $baz;', $b->closest(Filter::isInstanceOf('\Pharborist\Objects\ClassMemberListNode'))->getText());
  }

  public function testVisibility() {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { public $bar; }');
    /** @var ClassMemberNode $a */
    $a = $class_node->getProperties()[0];
    $this->assertEquals('private $bar;', $a->setVisibility('private')->parent()->parent()->getText());
  }
}
