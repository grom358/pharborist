<?php
namespace Pharborist\Index;

trait MethodContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $methods;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMethods() {
    return $this->methods;
  }

  /**
   * @return MethodIndex
   */
  public function getMethod($method) {
    return $this->getMethods()->get($method);
  }

  /**
   * @return boolean
   */
  public function hasMethod($method) {
    return $this->getMethods()->containsKey($method);
  }

  public function addMethod(MethodIndex $method) {
    $this->getMethods()->set($method->getName(), $method);
  }

  public function deleteMethod($method) {
    $this->getMethod($method)->delete();
  }

}
