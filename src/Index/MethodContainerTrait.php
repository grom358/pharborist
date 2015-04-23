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

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getOwnMethods() {
    // @TODO
  }

  /**
   * @return MethodIndex
   */
  public function getOwnMethod($method) {
    return $this->getOwnMethods()->get($method);
  }

  /**
   * @return boolean
   */
  public function hasOwnMethod($method) {
    return $this->getOwnMethods()->containsKey($method);
  }

  public function addMethod(MethodIndex $method) {
    $this->getMethods()->set($method->getName(), $method);
  }

  public function deleteMethod($method) {
    $this->getMethod($method)->delete();
  }

}
