<?php
namespace Pharborist\Index;

trait TraitConsumerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $traitsUsed;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTraitsUsed() {
    return $this->traitsUsed;
  }

  public function usesTrait($trait) {
    return $this->getTraitsUsed()->containsKey($trait);
  }

}
