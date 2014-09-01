<?php
namespace Pharborist;

/**
 * A block of statements.
 */
class StatementBlockNode extends ParentNode {
  /**
   * @return StatementNode[]
   */
  public function getStatements() {
    $matches = [];
    $child = $this->head;
    while ($child) {
      if ($child instanceof StatementNode) {
        $matches[] = $child;
      }
      elseif ($child instanceof StatementBlockNode) {
        $matches = array_merge($matches, $child->getStatements());
      }
      $child = $child->next;
    }
    return $matches;
  }

  /**
   * Add indent to each statement.
   *
   * @param string $whitespace
   *   Additional whitespace to add.
   */
  public function addIndent($whitespace) {
    /** @var WhitespaceNode $wsNode */
    foreach ($this->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->slice(0, -1) as $wsNode) {
      $text = $wsNode->getText();
      $wsNode->setText($text . $whitespace);
    }
  }

  /**
   * Set indent on each statement.
   *
   * @param string $whitespace
   *   Whitespace for indent.
   */
  public function setIndent($whitespace) {
    /** @var WhitespaceNode $wsNode */
    foreach ($this->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->slice(0, -1) as $wsNode) {
      $text = str_repeat(Settings::get('formatter.nl'), $wsNode->getNewlineCount()) . $whitespace;
      $wsNode->setText($text);
    }
  }
}
