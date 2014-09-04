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
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index is out of bounds.
   * @return Node
   */
  public function getItem($index) {
    if ($index < 0) {
      throw new \OutOfBoundsException('Index is out of bounds');
    }
    $i = 0;
    $child = $this->head;
    while ($child) {
      if ($child instanceof HiddenNode) {
        // ignore hidden nodes
      }
      elseif ($child instanceof TokenNode && $child->getType() === ',') {
        // ignore comma
      }
      else {
        if ($i === $index) {
          return $child;
        }
        $i++;
      }
      $child = $child->next;
    }
    throw new \OutOfBoundsException('Index is out of bounds');
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
      if ($index !== 0) {
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
   * Remove item.
   *
   * @param int|Node $item
   *   The index of item or item to remove.
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @throws \InvalidArgumentException
   *   Item does not exist in list.
   * @return $this
   */
  public function removeItem($item) {
    if (is_int($item)) {
      $index = $item;
      if ($index < 0) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $items = $this->getItems();
      if ($index >= count($items)) {
        throw new \OutOfBoundsException('index out of bounds');
      }
      $item = $items[$index];
      $is_last = $index === count($items) - 1;
    }
    else {
      $items = $this->getItems();
      if ($item->parent() !== $this) {
        throw new \InvalidArgumentException('invalid item');
      }
      $last_index = count($items) - 1;
      $last_item = $items[$last_index];
      $is_last = $last_item === $item;
    }
    if (count($items) === 1) {
      // No separators to remove.
    }
    elseif ($is_last) {
      $item->previousUntil(function ($node) {
        if ($node instanceof HiddenNode) {
          return FALSE;
        }
        if ($node instanceof TokenNode && $node->getType() === ',') {
          return FALSE;
        }
        return TRUE;
      })->remove();
    }
    else {
      $item->nextUntil(function ($node) {
        if ($node instanceof HiddenNode) {
          return FALSE;
        }
        if ($node instanceof TokenNode && $node->getType() === ',') {
          return FALSE;
        }
        return TRUE;
      })->remove();
    }
    $item->remove();
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
