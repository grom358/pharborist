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

  public function indent($indent, $level = 0) {
    $child = $this->head;
    while ($child) {
      $next = $child->next;
      if ($next === $this->tail && $next instanceof TokenNode && $next->getType() === '}') {
        $level--;
      }
      $child->indent($indent, $level);
      $child = $next;
    }
    return $this;
  }
}
