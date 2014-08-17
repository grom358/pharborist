<?php
namespace Pharborist;

trait FunctionTrait {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var NameNode
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
   * @return NameNode
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

  /**
   * @return $this
   */
  public function addParameter(ParameterNode $parameter) {
    $this->parameters
      ->append(new TokenNode(',', ','))
      ->append(new TokenNode(T_WHITESPACE, ' '))
      ->append($parameter);

    return $this;
  }
}
