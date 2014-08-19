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
   * Get the indent proceeding this node.
   *
   * @return string
   */
  protected function getIndent() {
    /** @var ParentNode $this */
    $whitespace_token = $this->firstToken()->previousToken();
    if ($whitespace_token->getType() !== T_WHITESPACE) {
      return '';
    }
    $lines = explode("\n", $whitespace_token->getText());
    $last_line = end($lines);
    return $last_line;
  }

  /**
   * @param DocCommentNode $comment
   * @return $this
   */
  public function setDocComment(DocCommentNode $comment) {
    if (isset($this->docComment)) {
      $this->docComment->replaceWith($comment);
    }
    else {
      $indent = $this->getIndent();
      $comment->setIndent($indent);
      $nl = Settings::get('formatter.nl');
      /** @var ParentNode $this */
      $this->firstChild()->before([
        $comment,
        WhitespaceNode::create($nl . $indent),
      ]);
    }
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @param boolean $is_reference
   * @return $this
   */
  public function setReference($is_reference) {
    if ($is_reference) {
      if (!isset($this->reference)) {
        $this->name->before(Token::reference());
      }
    }
    else {
      if (isset($this->reference)) {
        $this->reference->remove();
      }
    }
    return $this;
  }

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string|NameNode $name
   * @return $this
   */
  public function setName($name) {
    if (is_string($name)) {
      $name = NameNode::create($name);
    }
    $this->name->replaceWith($name);
    $this->name = $name;
    return $this;
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
   * Remove all parameters.
   *
   * @return $this
   */
  public function clearParameters() {
    $this->parameters->clearParameters();
    return $this;
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
