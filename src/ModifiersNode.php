<?php
namespace Pharborist;

/**
 * Method/member modifiers.
 */
class ModifiersNode extends ParentNode {
  protected $properties = array(
    'abstract' => NULL,
    'final' => NULL,
    'static' => NULL,
    'visibility' => NULL,
  );

  /**
   * @return Node
   */
  public function getAbstract() {
    return $this->properties['abstract'];
  }

  /**
   * @return Node
   */
  public function getFinal() {
    return $this->properties['final'];
  }

  /**
   * @return Node
   */
  public function getStatic() {
    return $this->properties['static'];
  }

  /**
   * @return Node
   */
  public function getVisibility() {
    return $this->properties['visibility'];
  }
}
