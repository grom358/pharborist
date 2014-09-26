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

  public function testAddTo() {
    /** @var ClassNode $source */
    $source = Parser::parseSnippet('class Foo { protected $bar; }');
    /** @var ClassNode $target */
    $target = Parser::parseSnippet('class Bar {}');
    /** @var ClassMemberListNode $property */
    $property_list = $source->getBody()->firstChild();

    $property_list->addTo($target);
    $this->assertFalse($source->hasProperty('bar'));
    $this->assertTrue($target->hasProperty('bar'));
    $this->assertSame($property_list, $target->getProperty('bar')->parent()->parent());
  }

  public function testCloneInto() {
    /** @var ClassNode $source */
    $source = Parser::parseSnippet('class Foo { protected $bar; }');
    /** @var ClassNode $target */
    $target = Parser::parseSnippet('class Bar {}');
    /** @var ClassMemberListNode $property_list */
    $original_list = $source->getBody()->firstChild();

    $cloned_list = $original_list->cloneInto($target);
    $this->assertInstanceOf('\Pharborist\ClassMemberListNode', $cloned_list);
    $this->assertNotSame($original_list, $cloned_list);
    $this->assertTrue($source->hasProperty('bar'));
    $this->assertTrue($target->hasProperty('bar'));
  }
}
