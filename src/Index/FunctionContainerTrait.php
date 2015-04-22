<?php
namespace Pharborist\Index;

trait FunctionContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $functions;

  public function getFunctions() {
    return $this->functions;
  }

  public function getFunction($function) {
    return $this->getFunctions()->get($function);
  }

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
