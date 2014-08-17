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
    $parameters = $this->getParameters();
    if (empty($parameters)) {
      $this->parameters->firstChild()->after($parameter);
    }
    else {
      $this->parameters->firstChild()->after([
        $parameter,
        new TokenNode(',', ','),
        new TokenNode(T_WHITESPACE, ' '),
      ]);
    }
    return $this;
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function appendParameter(ParameterNode $parameter) {
    $parameters = $this->getParameters();
    if (empty($parameters)) {
      $this->parameters->firstChild()->after($parameter);
    }
    else {
      $last_parameter = $parameters[count($parameters) - 1];
      $last_parameter->after([
        new TokenNode(',', ','),
        new TokenNode(T_WHITESPACE, ' '),
        $parameter
      ]);
    }
    return $this;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
