<?php
namespace Pharborist;

use \SplBool as Boolean;

/**
 * Method/member modifiers.
 */
class ModifiersNode extends ParentNode {
  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->abstract;
  }

  /**
   * @return $this
   */
  public function setAbstract(Boolean $status) {
    $this->abstract = ($status ? new TokenNode(T_ABSTRACT, 'abstract') : NULL);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }

  /**
   * @return $this
   */
  public function setFinal(Boolean $status) {
    $this->final = ($status ? new TokenNode(T_FINAL, 'final') : NULL);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @return $this
   */
  public function setStatic(Boolean $status) {
    $this->static = ($status ? new TokenNode(T_STATIC, 'static') : NULL);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }
}
