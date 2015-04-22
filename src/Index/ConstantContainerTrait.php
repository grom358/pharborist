<?php
namespace Pharborist\Index;

trait ConstantContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $constants;

  public function getConstants() {
    return $this->constants;
  }

  public function getConstant($constant) {
    return $this->getConstants()->get($constant);
  }

  public function hasConstant($constant) {
    return $this->getConstants()->containsKey($constant);
  }

}
