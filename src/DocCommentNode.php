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

  public function indent($indent, $level = 0) {
    $nl = Settings::get('formatter.nl');
    $lines = explode($nl, $this->text);
    if (count($lines) === 1) {
      return $this;
    }
    $padding = str_repeat($indent, $level) . ' ';
    $comment = '';
    $last_index = count($lines) - 1;
    foreach ($lines as $i => $line) {
      if ($i === 0) {
        $comment .= trim($line) . $nl;
      }
      elseif ($i === $last_index) {
        $comment .= $padding . trim($line);
      }
      else {
        $comment .= $padding . trim($line) . $nl;
      }
    }
    $this->setText($comment);
    return $this;
  }
}
