<?php
namespace Pharborist;

class DocCommentTest extends \PHPUnit_Framework_TestCase {
  public function testAddDocComment() {
    $original = <<<'EOF'
<?php
interface Test {
  public function test();
}
EOF;
    $expected = <<<'EOF'
<?php
interface Test {
  /**
   * Test
   */
  public function test();
}
EOF;
    $tree = Parser::parseSource($original);
    /** @var \Pharborist\Objects\InterfaceNode $interface */
    $interface = $tree->getStatements()[0];
    /** @var \Pharborist\Objects\InterfaceMethodNode $method */
    $method = $interface->getStatements()[0];
    $comment = DocCommentNode::create('Test');
    $method->setDocComment($comment);
    $this->assertEquals($expected, $tree->getText());
  }
}
