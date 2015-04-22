<?php
namespace Pharborist\Index;

trait PropertyContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $properties;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * @return PropertyIndex
   */
  public function getProperty($property) {
    return $this->getProperties()->get($property);
  }

  /**
   * @return boolean
   */
  public function hasProperty($property) {
    return $this->getProperties()->containsKey($property);
  }

  public function addProperty(PropertyIndex $property) {
    $this->getProperties()->set($property->getName(), $property);
  }

  public function deleteProperty($property) {
    $this->getProperty($property)->delete();
  }

}
