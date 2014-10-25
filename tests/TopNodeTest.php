<?php

namespace Pharborist;

class TopNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $doc = TopNode::create();
    $this->assertEquals("<?php\n", $doc->getText());

    $doc = TopNode::create('Pharborist\Test');
    $this->assertEquals("<?php\n\nnamespace Pharborist\\Test;\n", $doc->getText());

    $ns = $doc->children(Filter::isInstanceOf('\Pharborist\Namespaces\NamespaceNode'))[0];
    $this->assertEquals('\Pharborist\Test', $ns->getName()->getAbsolutePath());
  }

  public function testNSHelpers() {
    $doc = TopNode::create('Pharborist');

    $this->assertTrue($doc->hasNamespace('Pharborist'));
    $this->assertFalse($doc->hasNamespace('\Drupal'));
    $this->assertContains('Pharborist', $doc->getNamespaceNames());
    $this->assertContains('\Pharborist', $doc->getNamespaceNames(TRUE));

    $namespaces = $doc->getNamespaces();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $namespaces);
    $this->assertCount(1, $namespaces);

    $this->assertNull($doc->getNamespace('Drupal'));
    $ns = $doc->getNamespace('Pharborist');
    $this->assertInstanceOf('\Pharborist\Namespaces\NamespaceNode', $ns);
    $this->assertSame($ns, $namespaces[0]);

    $code = <<<'END'
<?php
namespace RoundTable\
  Knights\
  MontyPython;

class Foo {}
END;
    $doc = Parser::parseSource($code);
    $this->assertTrue($doc->hasNamespace('RoundTable\Knights\MontyPython'));
    $this->assertContains('\RoundTable\Knights\MontyPython', $doc->getNamespaceNames(TRUE));
  }
}
