<?php
namespace Pharborist\Index;

/**
 * Index information about a trait.
 */
class TraitIndex extends BaseIndex {

  use ConstantContainerTrait;
  use MethodContainerTrait;
  use PropertyContainerTrait;
  use TraitConsumerTrait;

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $classesUsing;

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $traitsUsing;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getClassesUsing() {
    return $this->classesUsing;
  }

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTraitsUsing() {
    return $this->traitsUsing;
  }

  /**
   * @return boolean
   */
  public function usedByClass($class) {
    return $this->getClassesUsing()->containsKey($class);
  }

  /**
   * @return boolean
   */
  public function usedByTrait($trait) {
    return $this->getTraitsUsing()->containsKey($trait);
  }

  /**
   * @return boolean
   */
  public function isUsed() {
    return ($this->getClassesUsing()->count() > 0 || $this->getTraitsUsing()->count() > 0);
  }

  /**
   * @return boolean
   */
  public function usedBy($name) {
    return ($this->usedByClass($name) || $this->usedByTrait($name));
  }

}
