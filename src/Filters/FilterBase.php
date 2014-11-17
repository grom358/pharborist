<?php

namespace Pharborist\Filters;

use Pharborist\Node;
use Pharborist\ParentNodeInterface;

abstract class FilterBase implements FilterInterface {

  /**
   * @var callable[]
   */
  protected $callbacks = [];

  /**
   * @var string[]
   */
  protected $nodeTypes = [];

  /**
   * @var \Pharborist\Node
   */
  protected $origin;
  
  /**
   * @var CombinatorInterface
   */
  protected $combinator;

  public function __construct(Node $origin = NULL) {
    $this->origin = $origin;

    // Always provide a node type filter by default.
    $this->callbacks['instance_of'] = [ $this, 'isInstanceOf' ];

    // Match all conditions by default.
    $this->all();
  }

  public function isInstanceOf(Node $node) {
    return in_array(get_class($node), $this->nodeTypes);
  }

  /**
   * Match all configured conditions.
   *
   * @return $this
   */
  public function all() {
    $this->combinator = new AllCombinator();
    return $this;
  }

  /**
   * Match any configured condition.
   *
   * @return $this
   */
  public function any() {
    $this->combinator = new AnyCombinator();
    return $this;
  }

  public function __invoke(Node $node) {
    // Load all condition callbacks into the combinator, execute it, and
    // return the verdict.
    array_walk($this->callbacks, [ $this->combinator, 'add' ]);
    return $this->combinator($node);
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

  public function closest() {
    return $this->ensureOrigin()->closest($this);
  }

  public function furthest() {
    return $this->ensureOrigin()->furthest($this);
  }

  public function siblings() {
    return $this->ensureOrigin()->siblings($this);
  }

  public function previousIsMatch() {
    return $this->previous() instanceof Node;
  }

  public function previous() {
    return $this->ensureOrigin()->previous($this);
  }

  public function previousAll() {
    return $this->ensureOrigin()->previousAll($this);
  }

  public function previousUntil(callable $until, $inclusive = TRUE) {
    return $this->ensureOrigin()->previousUntil($until, $inclusive)->filter($this);
  }

  public function nextIsMatch() {
    return $this->next() instanceof Node;
  }

  public function next() {
    return $this->ensureOrigin()->next($this);
  }

  public function nextAll() {
    return $this->ensureOrigin()->nextAll($this);
  }

  public function nextUntil(callable $until, $inclusive = TRUE) {
    return $this->ensureOrigin()->nextUntil($until, $inclusive)->filter($this);
  }

}
