<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends ParentNode {
  protected $properties = array(
    'docComment' => NULL,
    'visibility' => NULL,
    'reference' => NULL,
    'name' => NULL,
    'parameters' => array(),
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->properties['visibility'];
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->properties['reference'];
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->properties['name'];
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->properties['parameters'];
  }
}
