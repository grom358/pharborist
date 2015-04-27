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
   * @param SourcePosition $position
   */
  public function __construct($type, $text, $position = NULL) {
    $this->type = $type;
    $this->text = $text;
    $this->position = $position;
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
    return self::typeName($this->type);
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
   * @return string
   */
  public function __toString() {
    return $this->text;
  }
}
