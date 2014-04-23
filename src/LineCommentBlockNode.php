<?php
namespace Pharborist;

/**
 * A block of line comments.
 */
class LineCommentBlockNode extends ParentNode {
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
}
