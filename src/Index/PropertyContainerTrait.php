<?php
namespace Pharborist\Index;

trait PropertyContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $properties;

  public function getProperties() {
    return $this->properties;
  }

  public function getProperty($property) {
    return $this->getProperties()->get($property);
  }

  public function hasProperty($property) {
    return $this->getProperties()->containsKey($property);
  }

}
