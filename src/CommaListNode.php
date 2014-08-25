<?php
namespace Pharborist;

/**
 * Comma separated list.
 */
class CommaListNode extends ParentNode {
  /**
   * @return Node[]
   */
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

  /**
   * Prepend item.
   *
   * @param Node $item
   * @return $this
   */
  public function prependItem(Node $item) {
    $items = $this->getItems();
    if (empty($items)) {
      $this->append($item);
    }
    else {
      $this->prepend([
        $item,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Append item.
   *
   * @param Node $item
   * @return $this
   */
  public function appendItem(Node $item) {
    $items = $this->getItems();
    if (empty($items)) {
      $this->append($item);
    }
    else {
      $this->append([
        Token::comma(),
        Token::space(),
        $item,
      ]);
    }
    return $this;
  }

  /**
   * Insert item before index.
   *
   * @param Node $item
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @return $this
   */
  public function insertItem(Node $item, $index) {
    $items = $this->getItems();
    if (empty($items)) {
      if ($index > 0) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $this->append($item);
    }
    else {
      $max_index = count($items) - 1;
      if ($index < 0 || $index > $max_index) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $items[$index]->before([
        $item,
        Token::comma(),
        Token::space(),
      ]);
    }
    return $this;
  }

  /**
   * Pop an item off end of the list.
   *
   * @return Node
   *   The removed item. NULL if item list is empty.
   */
  public function pop() {
    $items = $this->getItems();
    if (empty($items)) {
      return NULL;
    }
    if (count($items) === 1) {
      $pop_item = $items[0];
      $pop_item->remove();
      return $pop_item;
    }
    $pop_item = $items[count($items) - 1];
    $pop_item->previousUntil(function ($node) {
      if ($node instanceof HiddenNode) {
        return FALSE;
      }
      if ($node instanceof TokenNode && $node->getType() === ',') {
        return FALSE;
      }
      return TRUE;
    })->remove();
    $pop_item->remove();
    return $pop_item;
  }

  /**
   * Shift an item off start of the list.
   *
   * @return Node
   *   The removed item. NULL if item list is empty.
   */
  public function shift() {
    $items = $this->getItems();
    if (empty($items)) {
      return NULL;
    }
    if (count($items) === 1) {
      $pop_item = $items[0];
      $pop_item->remove();
      return $pop_item;
    }
    $pop_item = $items[0];
    $pop_item->nextUntil(function ($node) {
      if ($node instanceof HiddenNode) {
        return FALSE;
      }
      if ($node instanceof TokenNode && $node->getType() === ',') {
        return FALSE;
      }
      return TRUE;
    })->remove();
    $pop_item->remove();
    return $pop_item;
  }
}
