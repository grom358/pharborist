<?php
namespace Pharborist;

/**
 * A function declaration.
 */
class FunctionDeclarationNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

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
