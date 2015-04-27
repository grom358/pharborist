<?php
namespace Pharborist;

use Pharborist\Objects\ClassMethodNode;

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

  public function testClassMethodNode() {
    $method = ClassMethodNode::create('foo');
    $method->setDocComment(DocCommentNode::create('{@inheritdoc}'));
    $expected = <<<'END'
/**
 * {@inheritdoc}
 */
public function foo() {}
END;
    $this->assertEquals($expected, $method->getText());
  }
}
