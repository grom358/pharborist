<?php

namespace Pharborist;

class TopNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $doc = TopNode::create();
    $this->assertEquals("<?php\n", $doc->getText());

    $doc = TopNode::create('Pharborist');
    $this->assertEquals("<?php\n\nnamespace Pharborist;\n", $doc->getText());
  }

  public function testNSHelpers() {
    $doc = TopNode::create('Pharborist');

    $this->assertTrue($doc->hasNamespace('Pharborist'));
    $this->assertFalse($doc->hasNamespace('\Drupal'));
    $this->assertSame(['Pharborist'], $doc->getNamespaceNames());

    $namespaces = $doc->getNamespaces();
    $this->assertInstanceOf('\Pharborist\NodeCollection', $namespaces);
    $this->assertCount(1, $namespaces);

    $this->assertNull($doc->getNamespace('Drupal'));
    $ns = $doc->getNamespace('Pharborist');
    $this->assertInstanceOf('\Pharborist\NamespaceNode', $ns);
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
  }
}
