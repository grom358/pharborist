<?php
namespace Pharborist\Index;

trait MethodContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $methods;

  public function getMethods() {
    return $this->methods;
  }

  public function getMethod($method) {
    return $this->getMethods()->get($method);
  }

  public function hasConstant($method) {
    return $this->getMethods()->containsKey($method);
  }

  public function addMethod(MethodIndex $method) {
    $this->getMethods()->set($method->getName(), $method);
  }

  public function deleteMethod($method) {
    $this->getMethod($method)->delete();
  }

}
