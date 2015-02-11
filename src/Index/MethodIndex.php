<?php
namespace Pharborist\Index;

class MethodIndex extends FunctionIndex {

  /**
   * @var string
   */
  private $visibility;

  /**
   * Get the visibility of the method.
   *
   * @return string
   *   Either public, protected or private.
   */
  public function getVisibility() {
    return $this->visibility;
  }

}
