<?php
namespace Pharborist;

/**
 * A comment.
 */
class CommentNode extends HiddenNode {
  use UncommentTrait;

  // Comment types
  const DOC = '/**';
  const BLOCK = '/*';
  const SINGLE = '//';
  const HASH = '#';

  /**
   * @var string
   */
  protected $commentType;

  /**
   * Construct token.
   * @param int $type
   * @param string $text
   * @param SourcePosition $position
   */
  public function __construct($type, $text, $position = NULL) {
    parent::__construct($type, $text, $position);
    $prefix = substr($text, 0, 3);
    if ($prefix === self::DOC) {
      $this->commentType = self::DOC;
    }
    else {
      $prefix = substr($prefix, 0, 2);
      if ($prefix === self::BLOCK) {
        $this->commentType = self::BLOCK;
      }
      elseif ($prefix === self::SINGLE) {
        $this->commentType = self::SINGLE;
      }
      elseif ($prefix[0] === self::HASH) {
        $this->commentType = self::HASH;
      }
    }
  }

  /**
   * Create line comment.
   *
   * @param string $comment
   *   Comment without leading prefix.
   * @return CommentNode|LineCommentBlockNode
   */
  public static function create($comment) {
    $comment = trim($comment);
    $nl_count = substr_count($comment, "\n");
    if ($nl_count > 1) {
      return LineCommentBlockNode::create($comment);
    }
    else {
      return new CommentNode(T_COMMENT, '// ' . $comment . "\n");
    }
  }

  /**
   * @return string
   */
  public function getCommentType() {
    return $this->commentType;
  }

  /**
   * @return bool
   */
  public function isLineComment() {
    return $this->commentType === self::SINGLE || $this->commentType === self::HASH;
  }

  /**
   * @return string
   */
  public function getCommentText() {
    switch ($this->commentType) {
      case self::SINGLE:
      case self::HASH:
        $comment_text = rtrim(substr($this->text, strlen($this->commentType)));
        if ($comment_text[0] === ' ') {
          $comment_text = substr($comment_text, 1);
        }
        return $comment_text;
      case self::DOC:
        $lines = explode("\n", $this->text);
        if (count($lines) === 1) {
          return trim(substr($this->text, 3, -2));
        }
        else {
          $last_index = count($lines) - 1;
          unset($lines[0]); // ignore first line
          unset($lines[$last_index]); // ignore last line
          $comment = '';
          $first = TRUE;
          foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '' && $line[0] === '*') {
              if (!$first) {
                $comment .= "\n";
              }
              else {
                $first = FALSE;
              }
              $comment .= substr($line, 2);
            }
          }
          return $comment;
        }
      case self::BLOCK:
        return trim(substr($this->text, 2, -2));
      default:
        throw new \LogicException("Unhandled comment type in CommentNode::getCommentText()");
    }
  }

  /**
   * Add indent to comment.
   *
   * @param string $whitespace
   *   Additional whitespace to add.
   * @return $this
   */
  public function addIndent($whitespace) {
    $lines = explode("\n", $this->text);
    if (count($lines) === 1) {
      return $this;
    }
    $comment = '';
    $last_index = count($lines) - 1;
    foreach ($lines as $i => $line) {
      if ($i === 0) {
        $comment .= trim($line) . "\n";
      } elseif ($i === $last_index) {
        $comment .= $whitespace . rtrim($line);
      }
      else {
        $comment .=  $whitespace . rtrim($line) . "\n";
      }
    }
    $this->setText($comment);
    return $this;
  }
}
