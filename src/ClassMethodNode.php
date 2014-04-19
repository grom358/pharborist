<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends StatementNode {
  protected $properties = array(
    'docComment' => NULL,
    'modifiers' => NULL,
    'reference' => NULL,
    'name' => NULL,
    'parameters' => NULL,
    'body' => NULL,
  );

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->properties['docComment'];
  }

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
    /** @var ParameterListNode $parameters */
    $parameters = $this->properties['parameters'];
    return $parameters->getParameters();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
