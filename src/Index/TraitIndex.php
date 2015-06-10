<?php
namespace Pharborist\Index;

class TraitIndex extends SingleInheritanceIndex {

  /**
   * @var string[]
   */
  protected $usedByTraits;

  /**
   * @var string[]
   */
  protected $usedByClasses;

  /**
   * @param FilePosition $position
   * @param string $name
   */
  public function __construct(FilePosition $position, $name) {
    parent::__construct($position, $name);
  }

  /**
   * Clear derived information from index.
   *
   * @internal Used by Indexer.
   *
   * @return $this
   */
  public function clear() {
    parent::clear();
    $this->usedByTraits = [];
    $this->usedByClasses = [];
    return $this;
  }

  /**
   * @return string[]
   */
  public function getUsedByTraits() {
    return $this->usedByTraits;
  }

  /**
   * Add trait as user of trait.
   *
   * @internal Used by Indexer.
   *
   * @param string $trait_fqn
   *   Fully qualified trait name.
   *
   * @return $this
   */
  public function addUsedByTrait($trait_fqn) {
    $this->usedByTraits[] = $trait_fqn;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getUsedByClasses() {
    return $this->usedByClasses;
  }

  /**
   * Add class as user of trait.
   *
   * @internal Used by Indexer.
   *
   * @param string $class_fqn
   *   Fully qualified class name.
   *
   * @return $this
   */
  public function addUsedByClass($class_fqn) {
    $this->usedByClasses[] = $class_fqn;
    return $this;
  }

}
