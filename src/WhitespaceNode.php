<?php
namespace Pharborist;

/**
 * Whitespace.
 */
class WhitespaceNode extends HiddenNode {
  private $newlineCount;

  public function __construct($type, $text, $position = NULL) {
    parent::__construct($type, $text, $position);
    if (!$position) {
      $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
      $this->newlineCount = substr_count($this->getText(), $nl);
    }
    else {
      /** @var SourcePosition $position */
      $this->newlineCount = $position->getNewlineCount();
    }
  }

  public static function create($whitespace) {
    return new WhitespaceNode(T_WHITESPACE, $whitespace);
  }

  /**
   * @return int
   */
  public function getNewlineCount() {
    return $this->newlineCount;
  }

  public function setText($text) {
    $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
    $this->newlineCount = substr_count($text, $nl);
    return parent::setText($text);
  }
}
