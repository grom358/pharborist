<?php
namespace Pharborist;

/**
 * A statement.
 */
abstract class StatementNode extends ParentNode {
  /**
   * Gets the number of lines spanned by this statement.
   *
   * @return integer
   *  Always returns at least one, because any statement will be at least
   *  one line long.
   */
  public function getLineCount() {
    $count = 1;

    $this
      ->find(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))
      ->each(function(WhitespaceNode $node) use (&$count) {
        $count += $node->getNewlineCount();
      });

    return $count;
  }

  /**
   * Creates a commented-out version of this statement.
   *
   * @return \Pharborist\CommentNode|\Pharborist\LineCommentBlockNode
   */
  public function toComment() {
    return CommentNode::create($this->getText());
  }
}
