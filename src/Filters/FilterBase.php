<?php

namespace Pharborist\Filters;

use Pharborist\Node;
use Pharborist\ParentNodeInterface;

abstract class FilterBase implements Filter {

  /**
   * @var \Pharborist\Node
   */
  protected $origin;

  public function __construct(Node $origin = NULL) {
    $this->origin = $origin;
  }

  public function isMatch() {
    if (isset($this->origin)) {
      return $this($this->origin);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function hasMatch() {
    if ($this->origin instanceof ParentNodeInterface) {
      return $this->origin->has($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function find() {
    if ($this->origin instanceof ParentNodeInterface) {
      return $this->origin->find($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchChildren() {
    if ($this->origin instanceof ParentNodeInterface) {
      return $this->origin->children($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchParent() {
    if (isset($this->origin)) {
      return $this($this->origin->parent());
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchParents() {
    if (isset($this->origin)) {
      return $this->origin->parents($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchSiblings() {
    if (isset($this->origin)) {
      return $this->origin->siblings($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchPrevious() {
    if (isset($this->origin)) {
      return $this($this->origin->previous());
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchPreviousUntil(callable $until, $inclusive = FALSE) {
    if (isset($this->origin)) {
      return $this->origin->previousUntil($until, $inclusive)->filter($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchPreviousAll() {
    if (isset($this->origin)) {
      return $this->origin->previousAll($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchNext() {
    if (isset($this->origin)) {
      return $this($this->origin->next());
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchNextUntil(callable $until, $inclusive = FALSE) {
    if (isset($this->origin)) {
      return $this->origin->nextUntil($until, $inclusive)->filter($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function matchNextAll() {
    if (isset($this->origin)) {
      return $this->origin->nextAll($this);
    }
    else {
      throw new \BadMethodCallException();
    }
  }

}
