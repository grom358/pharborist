<?php
namespace Pharborist;

/**
 * Whitespace.
 */
class WhitespaceNode extends HiddenNode {
  public static function create($whitespace) {
    return new WhitespaceNode(T_WHITESPACE, $whitespace);
  }

  /**
   * @return int
   */
  public function getNewlineCount() {
    $nl = FormatterFactory::getDefaultFormatter()->getConfig('nl');
    return substr_count($this->getText(), $nl);
  }
}
