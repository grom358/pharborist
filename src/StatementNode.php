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
    return $this
      ->find(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))
      ->filter(function(WhitespaceNode $node) {
        return $node->getNewlineCount() > 0;
      })
      ->count();
  }

}
