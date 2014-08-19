<?php
namespace Pharborist;

trait FunctionTrait {
  use ParameterTrait;

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
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
