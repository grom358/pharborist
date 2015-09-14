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
   * @var string
   */
  protected $filename;

  /**
   * @var int
   */
  protected $lineNo;

  /**
   * @var int
   */
  protected $newlineCount;

  /**
   * @var int
   */
  protected $colNo;

  /**
   * @var int
   */
  protected $byteOffset;

  /**
   * Construct token.
   * @param int $type
   * @param string $text
   * @param string $filename
   * @param int $lineNo
   * @param int $newlineCount
   * @param int $colNo
   * @param int $byteOffset
   */
  public function __construct($type, $text, $filename = 'source', $lineNo = -1, $newlineCount = -1, $colNo = -1, $byteOffset = -1) {
    $this->type = $type;
    $this->text = $text;
    $this->filename = $filename;
    $this->lineNo = $lineNo;
    $this->newlineCount = $newlineCount;
    $this->colNo = $colNo;
    $this->byteOffset = $byteOffset;
  }

  /**
   * @return string
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * @return int
   */
  public function getLineNumber() {
    return $this->lineNo;
  }

  /**
   * @return int
   */
  public function getNewlineCount() {
    return $this->newlineCount;
  }

  /**
   * @return int
   */
  public function getColumnNumber() {
    return $this->colNo;
  }

  /**
   * @return int
   */
  public function getByteOffset() {
    return $this->byteOffset;
  }

  /**
   * @return int
   */
  public function getByteLength() {
    return strlen($this->text);
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
