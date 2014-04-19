<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends StatementNode implements InterfaceStatementNode {
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
}
