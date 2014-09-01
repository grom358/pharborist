<?php
namespace Pharborist;

/**
 * A doc comment.
 */
class DocCommentNode extends CommentNode {
  /**
   * Create PHPDoc comment.
   *
   * @param string $comment
   *   Comment without asterisks but formatted into lines.
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
        $comment .= ' ' . trim($line);
      }
      else {
        $comment .= ' ' . trim($line) . "\n";
      }
    }
    $this->setText($comment);
    return $this;
  }
}
