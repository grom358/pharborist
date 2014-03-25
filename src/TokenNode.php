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
   * @return string
   */
  public function __toString() {
    return $this->text;
  }
}
