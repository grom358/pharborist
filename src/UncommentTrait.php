<?php
namespace Pharborist;

/**
 * Contains the uncomment() method for line comments (they can't extend a single
 * abstract base class because they're each based on a different node type).
 */
trait UncommentTrait {
  /**
   * Uncomments the contents of this comment.
   *
   * @return Node
   */
  public function uncomment() {
    /** @var CommentNode|LineCommentBlockNode $this */
    return Parser::parseSnippet($this->getCommentText());
  }
}
