<?php
namespace Pharborist\Index;

trait TraitContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $traits;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTraits() {
    return $this->traits;
  }

  /**
   * @return TraitIndex
   */
  public function getTrait($trait) {
    return $this->getTraits()->get($trait);
  }

  /**
   * @return boolean
   */
  public function hasTrait($trait) {
    return $this->getTraits()->containsKey($trait);
  }

  public function addTrait(TraitIndex $trait) {
    $this->getTraits()->set($trait->getName(), $trait);
  }

  public function deleteTraits() {
    foreach ($this->getTraits() as $trait) {
      $trait->delete();
    }
  }

  public function deleteTrait($trait) {
    $this->getTrait($trait)->delete();
  }

}
