<?php
namespace Pharborist\Index;

abstract class ExtendableIndex extends BaseIndex {

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $extending;

  /**
   * @var \Doctrine\Common\Collections\Collection
   */
  protected $extendedBy;

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getExtending() {
    return $this->extending;
  }

  /**
   * @return boolean
   */
  public function isExtending($parent) {
    return $this->getExtending()->containsKey($parent);
  }

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getExtendedBy() {
    return $this->extendedBy;
  }

  /**
   * @return boolean
   */
  public function isExtendedBy($child) {
    return $this->getExtendedBy()->containsKey($child);
  }

}
