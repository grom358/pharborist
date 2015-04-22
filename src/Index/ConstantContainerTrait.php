<?php
namespace Pharborist\Index;

trait ConstantContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $constants;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getConstants() {
    return $this->constants;
  }

  /**
   * @return ConstantIndex
   */
  public function getConstant($constant) {
    return $this->getConstants()->get($constant);
  }

  /**
   * @return boolean
   */
  public function hasConstant($constant) {
    return $this->getConstants()->containsKey($constant);
  }

  public function addConstant(ConstantIndex $constant) {
    $this->getConstants()->set($constant->getName(), $constant);
  }

  public function deleteConstant($constant) {
    $this->getConstant($constant)->delete();
  }

}
