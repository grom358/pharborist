<?php
namespace Pharborist;

/**
 * A token.
 */
class TokenNode extends Node {
  /**
   * @var int
   */
  protected $type;

  /**
   * @var string
   */
  protected $text;

  /**
   * @var SourcePosition
   */
  protected $position;

  /**
   * Construct token.
   * @param int $type
   * @param string $text
   * @param int $line_num
   * @param int $col_num
   */
  public function __construct($type, $text, $line_num, $col_num) {
    $this->type = $type;
    $this->text = $text;
    $this->position = new SourcePosition($line_num, $col_num);
  }

  /**
   * @return SourcePosition
   */
  public function getSourcePosition() {
    return $this->position;
  }

  /**
   * @return int
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param int $type
   * @return string
   */
  public static function typeName($type) {
    if (is_string($type)) {
      return $type;
    }
    else {
      return token_name($type);
    }
  }

  /**
   * @return string
   */
  public function getTypeName() {
    return self::getTypeName($this->type);
  }

  /**
   * @return string
   */
  public function getText() {
    return $this->text;
  }

  /**
   * @param string $text
   * @return $this
   */
  public function setText($text) {
    $this->text = $text;
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function previousToken() {
    $prev_node = $this->previousSibling();
    if ($prev_node === NULL) {
      $parent = $this->getParent();
      while ($parent !== NULL && $parent->previousSibling() === NULL) {
        $parent = $parent->getParent();
      }
      if ($parent === NULL) {
        return NULL;
      }
      $prev_node = $parent->previousSibling();
    }
    if ($prev_node instanceof ParentNode) {
      return $prev_node->getLastToken();
    }
    else {
      return $prev_node;
    }
  }

  /**
   * @return TokenNode
   */
  public function nextToken() {
    $next_node = $this->nextSibling();
    if ($next_node === NULL) {
      $parent = $this->getParent();
      while ($parent !== NULL && $parent->nextSibling() === NULL) {
        $parent = $parent->getParent();
      }
      if ($parent === NULL) {
        return NULL;
      }
      $next_node = $parent->nextSibling();
    }
    if ($next_node instanceof ParentNode) {
      return $next_node->getFirstToken();
    }
    else {
      return $next_node;
    }
  }

  /**
   * @return string
   */
  public function __toString() {
    return $this->text;
  }
}
