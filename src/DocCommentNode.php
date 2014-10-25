<?php
namespace Pharborist;

/**
 * A doc comment.
 */
class DocCommentNode extends CommentNode {
  /**
   * Creates a PHPDoc comment.
   *
   * @param string $comment
   *   The comment body without asterisks, but formatted into lines.
   *
   * @return DocCommentNode
   */
  public static function create($comment) {
    $comment = trim($comment);
    $lines = array_map('trim', explode("\n", $comment));
    $text = "/**\n";
    foreach ($lines as $i => $line) {
      $text .= ' * ' . $line . "\n";
    }
    $text .= ' */';
    return new DocCommentNode(T_DOC_COMMENT, $text);
  }

  /**
   * Set indent for document comment.
   *
   * @param string $indent
   *   Whitespace to use as indent.
   * @return $this
   */
  public function setIndent($indent) {
    $lines = explode("\n", $this->text);
    if (count($lines) === 1) {
      return $this;
    }
    $comment = '';
    $last_index = count($lines) - 1;
    foreach ($lines as $i => $line) {
      if ($i === 0) {
        $comment .= trim($line) . "\n";
      }
      elseif ($i === $last_index) {
        $comment .= $indent . ' ' . trim($line);
      }
      else {
        $comment .= $indent . ' ' . trim($line) . "\n";
      }
    }
    $this->setText($comment);
    return $this;
  }
}
