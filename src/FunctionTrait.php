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
   * @return ParameterListNode
   */
  public function getParameterList() {
    return $this->parameters;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->getParameters();
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function prependParameter(ParameterNode $parameter) {
    $this->parameters->prependParameter($parameter);
    return $this;
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function appendParameter(ParameterNode $parameter) {
    $this->parameters->appendParameter($parameter);
    return $this;
  }

  /**
   * Insert parameter before parameter at index.
   *
   * @param ParameterNode $parameter
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @return $this
   */
  public function insertParameter(ParameterNode $parameter, $index) {
    $this->parameters->insertParameter($parameter, $index);
    return $this;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
