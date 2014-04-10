<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ParentNode {
  protected $properties = array(
    'modifiers' => NULL,
    'reference' => NULL,
    'name' => NULL,
    'parameters' => array(),
    'body' => NULL,
  );

  /**
   * @return ModifiersNode
   */
  public function getModifiers() {
    return $this->properties['modifiers'];
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

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
