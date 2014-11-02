<?php

namespace Pharborist\Filters;

use Pharborist\Node;
use Pharborist\ParentNodeInterface;

abstract class FilterBase implements FilterInterface {

  /**
   * @var \Pharborist\Node
   */
  protected $origin;

  public function __construct(Node $origin = NULL) {
    $this->origin = $origin;
  }

  /**
   * @return \Pharborist\Node
   */
  protected function ensureOrigin() {
    if ($this->origin) {
      return $this->origin;
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  /**
   * @return \Pharborist\ParentNodeInterface
   */
  protected function ensureOriginIsParent() {
    if ($this->origin instanceof ParentNodeInterface) {
      return $this->origin;
    }
    else {
      throw new \BadMethodCallException();
    }
  }

  public function isMatch() {
    return $this->ensureOrigin()->is($this);
  }

  public function hasMatch() {
    return $this->ensureOriginIsParent()->has($this);
  }

  public function children() {
    return $this->ensureOriginIsParent()->children($this);
  }

  public function find() {
    return $this->ensureOriginIsParent()->find($this);
  }

  public function parentIsMatch() {
    $parent = $this->ensureOrigin()->parent();
    return isset($parent) ? $parent->is($this) : FALSE;
  }

  public function parents() {
    return $this->ensureOrigin()->parents($this);
  }

  public function siblings() {
    return $this->ensureOrigin()->siblings($this);
  }

  public function previousIsMatch() {
    $previous = $this->ensureOrigin()->previous();
    return isset($previous) ? $previous->is($this) : FALSE;
  }

  public function previousAll() {
    return $this->ensureOrigin()->previousAll($this);
  }

  public function previousUntil(callable $until, $inclusive = TRUE) {
    return $this->ensureOrigin()->previousUntil($until, $inclusive)->filter($this);
  }

  public function nextIsMatch() {
    $next = $this->ensureOrigin()->next();
    return isset($next) ? $next->is($this) : FALSE;
  }

  public function nextAll() {
    return $this->ensureOrigin()->nextAll($this);
  }

  public function nextUntil(callable $until, $inclusive = TRUE) {
    return $this->ensureOrigin()->nextUntil($until, $inclusive)->filter($this);
  }

}
