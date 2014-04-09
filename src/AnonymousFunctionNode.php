<?php
namespace Pharborist;

/**
 * An anonymous function.
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'reference' => NULL,
    'parameters' => array(),
    'lexicalVariables' => array(),
    'body' => NULL,
  );

  /**
   * @return Node
   */
  public function getReference() {
    return $this->properties['reference'];
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->properties['parameters'];
  }

  /**
   * @return (VariableNode|ReferenceVariableNode)[]
   */
  public function getLexicalVariables() {
    return $this->properties['lexicalVariables'];
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
