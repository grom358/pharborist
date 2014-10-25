<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  /**
   * @return StatementNode[]
   */
  public function getStatements() {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof StatementNode) {
        $matches[] = $child;
      }
      elseif ($child instanceof StatementBlockNode) {
        $matches = array_merge($matches, $child->getStatements());
      }
      $child = $child->next;
    }
    return $matches;
  }
}
