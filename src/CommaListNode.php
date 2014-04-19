<?php
namespace Pharborist;

/**
 * Comma separated list.
 */
class CommaListNode extends ParentNode {
  public function getItems() {
    $items = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof HiddenNode) {
        // ignore hidden nodes
      }
      elseif ($child instanceof TokenNode && $child->getType() === ',') {
        // ignore comma
      }
      else {
        $items[] = $child;
      }
      $child = $child->next;
    }
    return $items;
  }
}
