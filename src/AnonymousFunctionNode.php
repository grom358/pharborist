<?php
namespace Pharborist;

/**
 * An anonymous function.
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  protected $properties = array(
    'reference' => NULL,
    'parameters' => NULL,
    'lexicalVariables' => NULL,
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
    /** @var ParameterListNode $parameters */
    $parameters = $this->properties['parameters'];
    return $parameters->getParameters();
  }

  /**
   * @return (VariableNode|ReferenceVariableNode)[]
   */
  public function getLexicalVariables() {
    /** @var CommaListNode $var_list */
    $var_list = $this->properties['lexicalVariables'];
    return $var_list->getItems();
  }

  /**
   * @return Node
   */
  public function getBody() {
    return $this->properties['body'];
  }
}
