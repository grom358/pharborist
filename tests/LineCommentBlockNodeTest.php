<?php
namespace Pharborist;

class LineCommentBlockNodeTest extends \PHPUnit_Framework_TestCase {
  public function testCreate() {
    $comment = <<<'EOF'
hello
world

EOF;
    $line_comment_text = <<<'EOF'
// hello
// world

EOF;
    $comment_node = LineCommentBlockNode::create($comment);
    $this->assertEquals($line_comment_text, $comment_node->getText());

    $comment_node->addIndent('  ');
    $expected = <<<'EOF'
  // hello
  // world

EOF;
    $this->assertEquals($expected, $comment_node->getText());

    $comment_node->addIndent('  ');
    $expected = <<<'EOF'
    // hello
    // world

EOF;
    $this->assertEquals($expected, $comment_node->getText());

    $comment_node->removeIndent();
    $this->assertEquals($line_comment_text, $comment_node->getText());
  }
}
