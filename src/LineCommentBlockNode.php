<?php
namespace Pharborist;

/**
 * A block of line comments, e.g.:
 * ```
 * // This is a haiku.
 * // Seriously, it is one.
 * // Isn't that awesome?
 * ```
 */
class LineCommentBlockNode extends ParentNode {
  use UncommentTrait;

  /**
   * Create line comment block.
   *
   * @param string $comment
   *   Comment without leading prefix.
   *
   * @return LineCommentBlockNode
   */
  public static function create($comment) {
    $block_comment = new LineCommentBlockNode();
    $comment = trim($comment);
    $lines = array_map('rtrim', explode("\n", $comment));
    foreach ($lines as $line) {
      $comment_node = new CommentNode(T_COMMENT, '// ' . $line . "\n");
      $block_comment->addChild($comment_node);
    }
    return $block_comment;
  }

  /**
   * @return string
   */
  public function getCommentText() {
    $comment = '';
    $child = $this->firstChild();
    $first = TRUE;
    while ($child) {
      if ($child instanceof CommentNode) {
        if ($first) {
          $first = FALSE;
        }
        else {
          $comment .= "\n";
        }
        $comment .= $child->getCommentText();
      }
      $child = $child->next;
    }
    return $comment;
  }

  /**
   * Set indent for document comment.
   *
   * @param string $indent
   *   Whitespace to use as indent.
   * @return $this
   */
  public function setIndent($indent) {
    // Normalize comment block.
    $this->removeIndent();
    // Add indent to comment block.
    $this->addIndent($indent);
    return $this;
  }

  /**
   * Remove indent from document comment.
   *
   * @return $this
   */
  public function removeIndent() {
    $this->children(function (Node $node) {
      return !($node instanceof CommentNode);
    })->remove();
    return $this;
  }

  /**
   * Add indent to comment.
   *
   * @param string $whitespace
   *   Additional whitespace to add.
   * @return $this
   */
  public function addIndent($whitespace) {
    $has_indent = $this->children(function (Node $node) {
      return !($node instanceof CommentNode);
    })->count() > 0;
    if ($has_indent) {
      $this->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->each(function (WhitespaceNode $ws_node) use ($whitespace) {
        $ws_node->setText($ws_node->getText() . $whitespace);
      });
    }
    else {
      $this->children()->before(Token::whitespace($whitespace));
    }
    return $this;
  }
}
