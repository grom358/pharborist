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
    $nl = Settings::get('formatter.nl');
    return substr_count($this->getText(), $nl);
  }
}
