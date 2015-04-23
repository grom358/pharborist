<?php
namespace Pharborist\Index;

trait InterfaceContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $interfaces;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getInterfaces() {
    return $this->interfaces;
  }

  /**
   * @return InterfaceIndex
   */
  public function getInterface($interface) {
    return $this->getInterfaces()->get($interface);
  }

  /**
   * @return boolean
   */
  public function hasInterface($interface) {
    return $this->getInterfaces()->containsKey($interface);
  }

  public function addInterface(InterfaceIndex $interface) {
    $this->getInterfaces()->set($interface->getName(), $interface);
  }

  public function deleteInterfaces() {
    foreach ($this->getInterfaces() as $interface) {
      $interface->delete();
    }
  }

  public function deleteInterface($interface) {
    $this->getInterface($interface)->delete();
  }

}
