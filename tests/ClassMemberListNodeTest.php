<?php

namespace Pharborist;

class ClassMemberListNodeTest extends \PHPUnit_Framework_TestCase {
  public function testStatic() {
    /** @var ClassMemberListNode $a */
    $a = Parser::parseSnippet('class Foo { public static $bar; }')->getBody()->firstChild();
    $this->assertTrue($a->isStatic());
    /** @var ClassMemberListNode $b */
    $b = Parser::parseSnippet('class Baz { protected $doodle; }')->getBody()->firstChild();
    $this->assertFalse($b->isStatic());

    $a->setStatic(FALSE);
    $this->assertFalse($a->isStatic());
    $this->assertEquals('public $bar;', $a->getText());

    $b->setStatic(TRUE);
    $this->assertTrue($b->isStatic());
    $this->assertEquals('protected static $doodle;', $b->getText());
  }

  /**
   * @expectedException \BadMethodCallException
   */
  public function testRemoveVisibility() {
    /** @var ClassMemberListNode $property */
    $property = Parser::parseSnippet('class Foo { public $wrassle; }')->getBody()->firstChild();
    $property->setVisibility(NULL);
  }
}
