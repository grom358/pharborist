<?php
namespace Pharborist;

use Pharborist\Namespaces\NamespaceNode;
use Pharborist\Objects\ClassNode;

class NamespaceNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $namespace_node = NamespaceNode::create('\Top\Sub');
    $this->assertEquals('\Top\Sub', $namespace_node->getName()->getAbsolutePath());
    $this->assertNotNull($namespace_node->getBody());
  }

  public function testUseDeclarations() {
    $snippet = <<<'EOF'
namespace Test {
  use const Other\MY_CONST;
  use function Other\my_func;
  use Other\MyClass;
  use Other\OtherClass as Bind;
  class TestClass {}
}
EOF;
    /** @var NamespaceNode $namespace */
    $namespace = Parser::parseSnippet($snippet);

    $declarations = $namespace->getUseDeclarations();
    $this->assertCount(4, $declarations);

    $aliases = $namespace->getClassAliases();
    $this->assertCount(2, $aliases);
    $this->assertArrayHasKey('MyClass', $aliases);
    $this->assertEquals('\Other\MyClass', $aliases['MyClass']);
    $this->assertArrayHasKey('Bind', $aliases);
    $this->assertEquals('\Other\OtherClass', $aliases['Bind']);

    $class_node = $namespace->find(Filter::isClass('TestClass'))[0];
    $this->assertTrue($namespace->owns($class_node));
    $class_node = ClassNode::create('Dummy');
    $this->assertFalse($namespace->owns($class_node));
  }
}
