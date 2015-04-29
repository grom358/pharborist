<?php
namespace Pharborist;

trait DocCommentTrait {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * Get the indent proceeding this node.
   *
   * @return string
   */
  public function getIndent() {
    /** @var ParentNode $this */
    $whitespace_token = $this->previousToken();
    if (empty($whitespace_token) || $whitespace_token->getType() !== T_WHITESPACE) {
      return '';
    }
    $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
    $lines = explode($nl, $whitespace_token->getText());
    $last_line = end($lines);
    return $last_line;
  }

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @param DocCommentNode $comment
   * @return $this
   */
  public function setDocComment(DocCommentNode $comment) {
    if (isset($this->docComment)) {
      $this->docComment->remove();
    }

    $indent = $this->getIndent();
    $comment->setIndent($indent);
    $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
    /** @var ParentNode $this */
    $this->firstChild()->before([
      $comment,
      WhitespaceNode::create($nl . $indent),
    ]);
    $this->docComment = $comment;

    return $this;
  }
}
