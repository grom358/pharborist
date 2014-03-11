<?php
namespace Pharborist;

/**
 * A PHP Token.
 * @package Pharborist
 */
class Token {
  public $type;
  public $text;
  public $lineNo;
  public $colNo;

  public function __construct($type, $text, $lineNo = NULL, $colNo = NULL) {
    $this->type = $type;
    $this->text = $text;
    $this->lineNo = $lineNo;
    $this->colNo = $colNo;
  }

  static public function typeName($type) {
    if ($type === NULL) {
      return "NULL";
    }
    if (is_string($type)) {
      return $type;
    }
    else {
      return token_name($type);
    }
  }

  public function getTypeName() {
    return self::typeName($this->type);
  }

  public function __toString() {
    return $this->text;
  }

  public function debugString() {
    return $this->lineNo . ':' . $this->colNo . ' ' . $this->getTypeName() . '|' . $this->text;
  }
}
