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

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getOwnProperties() {
    // @TODO
  }

  /**
   * @return PropertyIndex
   */
  public function getOwnProperty($property) {
    return $this->getOwnProperties()->get($property);
  }

  /**
   * @return boolean
   */
  public function hasOwnProperty($property) {
    return $this->getOwnProperties()->containsKey($property);
  }

  public function addProperty(PropertyIndex $property) {
    $this->getProperties()->set($property->getName(), $property);
  }

  public function deleteProperties() {
    foreach ($this->getProperties() as $property) {
      $property->delete();
    }
  }

  public function deleteProperty($property) {
    $this->getProperty($property)->delete();
  }

}
