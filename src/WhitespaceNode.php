<?php
namespace Pharborist;

/**
 * Whitespace.
 */
class WhitespaceNode extends HiddenNode {
  /**
   * @var int
   */
  protected $newlineCount;

  /**
   * Construct token.
   * @param int $type
   * @param string $text
   * @param SourcePosition $position
   * @param int $newline_count
   */
  public function __construct($type, $text, $position = NULL, $newline_count = 0) {
    parent::__construct($type, $text, $position);
    $this->newlineCount = $newline_count;
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

  public function indent($indent, $level = 0) {
    if ($this->newlineCount > 0) {
      // Only indent multi-line whitespace.
      $nl = Settings::get('formatter.nl');
      $this->text = str_repeat($nl, $this->newlineCount) . str_repeat($indent, $level);
    }
  }
}
