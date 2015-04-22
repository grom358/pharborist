<?php
namespace Pharborist\Index;

trait TraitContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $traits;

  public function getTraits() {
    return $this->traits;
  }

  public function getTrait($trait) {
    return $this->getTraits()->get($trait);
  }

  public function hasTrait($trait) {
    return $this->getTraits()->containsKey($trait);
  }

}
