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

  public function testStatic() {
    /** @var ClassMemberNode $a */
    $a = Parser::parseSnippet('class Foo { public $bar; }')->getBody()->firstChild()->getMembers()[0];
    $this->assertFalse($a->isStatic());
    $this->assertNull($a->getStatic());
    $a->setStatic(TRUE);
    $this->assertTrue($a->isStatic());
    $this->assertSame('public static $bar;', $a->closest(Filter::isInstanceOf('\Pharborist\ClassMemberListNode'))->getText());

    /** @var ClassMemberNode $b */
    $b = Parser::parseSnippet('class Bar { protected static $baz; }')->getBody()->firstChild()->getMembers()[0];
    $this->assertTrue($b->isStatic());
    $this->assertInstanceOf('\Pharborist\TokenNode', $b->getStatic());
    $this->assertSame(T_STATIC, $b->getStatic()->getType());
    $b->setStatic(FALSE);
    $this->assertFalse($b->isStatic());
    $this->assertSame('protected $baz;', $b->closest(Filter::isInstanceOf('\Pharborist\ClassMemberListNode'))->getText());
  }

  public function testVisibility() {
    $a = Parser::parseSnippet('class Foo { public $bar; }')->getBody()->firstChild()->getMembers()[0];
    $this->assertEquals('private $bar;', $a->setVisibility('private')->parent()->parent()->getText());
  }
}
