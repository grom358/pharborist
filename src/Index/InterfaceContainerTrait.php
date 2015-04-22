<?php
namespace Pharborist\Index;

trait InterfaceContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $interfaces;

  public function getInterfaces() {
    return $this->interfaces;
  }

  public function getInterface($interface) {
    return $this->getInterfaces()->get($interface);
  }

  public function hasInterface($interface) {
    return $this->getInterfaces()->containsKey($interface);
  }

  public function addInterface(InterfaceIndex $interface) {
    $this->getInterfaces()->set($interface->getName(), $interface);
  }

  public function deleteInterface($interface) {
    $this->getInterface($interface)->delete();
  }

}
