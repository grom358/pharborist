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

}
