<?php
namespace Pharborist;

/**
 * An interface method.
 */
class InterfaceMethodNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $visibility;

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
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
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
