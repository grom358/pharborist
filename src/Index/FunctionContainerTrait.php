<?php
namespace Pharborist\Index;

trait FunctionContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $functions;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getFunctions() {
    return $this->functions;
  }

  /**
   * @return FunctionIndex
   */
  public function getFunction($function) {
    return $this->getFunctions()->get($function);
  }

  /**
   * @return boolean
   */
  public function hasFunction($function) {
    return $this->getFunctions()->containsKey($function);
  }

  public function addFunction(FunctionIndex $function) {
    $this->getFunctions()->set($function->getName(), $function);
  }

  public function deleteFunction($function) {
    $this->getFunction($function)->delete();
  }

}
