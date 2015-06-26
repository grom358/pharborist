<?php
namespace Pharborist;

/**
 * Whitespace.
 */
class WhitespaceNode extends HiddenNode {
  public function __construct($type, $text, $lineNo = -1, $newlineCount = -1, $colNo = -1, $byteOffset = -1) {
    parent::__construct($type, $text, $lineNo, $newlineCount, $colNo, $byteOffset);
    if ($newlineCount < 0) {
      $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
      $this->newlineCount = substr_count($this->getText(), $nl);
    }
  }

  public static function create($whitespace) {
    return new WhitespaceNode(T_WHITESPACE, $whitespace);
  }

  public function setText($text) {
    $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
    $this->newlineCount = substr_count($text, $nl);
    return parent::setText($text);
  }
}
