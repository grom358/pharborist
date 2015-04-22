<?php
namespace Pharborist\Index;

trait ClassContainerTrait {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $classes;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getClasses() {
    return $this->classes;
  }

  /**
   * @return ClassIndex
   */
  public function getClass($class) {
    return $this->getClasses()->get($class);
  }

  /**
   * @return boolean
   */
  public function hasClass($class) {
    return $this->getClasses()->containsKey($class);
  }

  public function addClass(ClassIndex $class) {
    $this->getClasses()->set($class->getName(), $class);
  }

  public function deleteClass($class) {
    $this->getClass($class)->delete();
  }

}
