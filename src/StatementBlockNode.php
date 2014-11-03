<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  protected function _getStatements() {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof StatementNode) {
        $matches[] = $child;
      }
      elseif ($child instanceof StatementBlockNode) {
        $matches = array_merge($matches, $child->_getStatements());
      }
      $child = $child->next;
    }
    return $matches;
  }

  /**
   * @return NodeCollection|StatementNode[]
   */
  public function getStatements() {
    return new NodeCollection($this->_getStatements(), FALSE);
  }
}
