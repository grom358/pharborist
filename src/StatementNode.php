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
   */
  public function getLineCount() {
    $count = 0;

    $this
      ->find(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))
      ->each(function(WhitespaceNode $node) use (&$count) {
        $count += $node->getNewlineCount();
      });

    return $count;
  }

}
