<?php
namespace Pharborist;

/**
 * Tests builder methods.
 */
class BuilderTest extends \PHPUnit_Framework_TestCase {
  public function testBuildClass() {
    $classNode = ClassNode::create('MyClass');
    $this->assertEquals('class MyClass {}', $classNode->getText());

    $classNode->setFinal(TRUE);
    $this->assertEquals('final class MyClass {}', $classNode->getText());

    $classNode->setFinal(FALSE);
    $this->assertEquals('class MyClass {}', $classNode->getText());

    $classNode->setName('MyTest');
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->setExtends('MyClass');
    $this->assertEquals('class MyTest extends MyClass {}', $classNode->getText());

    $classNode->setExtends('BaseClass');
    $this->assertEquals('class MyTest extends BaseClass {}', $classNode->getText());

    $classNode->setExtends(NULL);
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->setImplements('MyInterface');
    $this->assertEquals('class MyTest implements MyInterface {}', $classNode->getText());

    $classNode->setImplements('Yai');
    $this->assertEquals('class MyTest implements Yai {}', $classNode->getText());

    $classNode->setImplements(NULL);
    $this->assertEquals('class MyTest {}', $classNode->getText());

    $classNode->appendProperty('someProperty');
    $classNode->appendMethod('someMethod');

    $expected = <<<'EOF'
class MyTest {
  private $someProperty;

  public function someMethod() {}
}
EOF;
    $this->assertEquals($expected, $classNode->getText());
  }

  public function testClassMethod() {
    $method = ClassMethodNode::create('someMethod');
    $this->assertEquals('public function someMethod() {}', $method->getText());

    $method->setVisibility(Token::_protected());
    $this->assertEquals('protected function someMethod() {}', $method->getText());

    $method->setFinal(TRUE);
    $this->assertEquals('final protected function someMethod() {}', $method->getText());

    $method->setFinal(FALSE);
    $this->assertEquals('protected function someMethod() {}', $method->getText());

    $method->setStatic(TRUE);
    $this->assertEquals('protected static function someMethod() {}', $method->getText());

    $method->setStatic(FALSE);
    $this->assertEquals('protected function someMethod() {}', $method->getText());
  }

  public function testClassProperty() {
    $property = ClassMemberListNode::create('someProperty');
    $this->assertEquals('private $someProperty;', $property->getText());

    $property->setVisibility(Token::_protected());
    $this->assertEquals('protected $someProperty;', $property->getText());
  }
}
