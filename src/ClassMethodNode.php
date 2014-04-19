<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ClassStatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var ModifiersNode
   */
  protected $modifiers;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var ParameterListNode
   */
  protected $parameters;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return ModifiersNode
   */
  public function getModifiers() {
    return $this->modifiers;
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->getParameters();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
