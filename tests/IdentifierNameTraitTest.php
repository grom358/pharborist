<?php
namespace Pharborist;

class IdentifierNameTraitTest extends \PHPUnit_Framework_TestCase {
  public function testId() {
    $snippet = <<<'EOF'
namespace Test {
  function hello_world() {
    echo 'hello world!', PHP_EOL;
  }
}
EOF;
    /** @var \Pharborist\Namespaces\NamespaceNode $namespace_node */
    $namespace_node = Parser::parseSnippet($snippet);
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $namespace_node->getBody()->children(Filter::isFunction('hello_world'))[0];
    $this->assertEquals('hello_world', $function->getName()->getText());
    $this->assertSame($namespace_node, $function->getNamespace());
    $this->assertTrue($function->inNamespace($namespace_node));
    $this->assertTrue($function->inNamespace('\Test'));
    $this->assertFalse($function->inNamespace('\Dummy\Test'));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalid() {
    $snippet = <<<'EOF'
namespace Test {
  function hello_world() {
    echo 'hello world!', PHP_EOL;
  }
}
EOF;
    /** @var \Pharborist\Namespaces\NamespaceNode $namespace_node */
    $namespace_node = Parser::parseSnippet($snippet);
    /** @var \Pharborist\Functions\FunctionDeclarationNode $function */
    $function = $namespace_node->getBody()->children(Filter::isFunction('hello_world'))[0];
    $this->assertTrue($function->inNamespace(new \stdClass()));
  }
}
