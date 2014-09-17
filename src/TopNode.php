<?php
namespace Pharborist;

/**
 * Top node of tree.
 */
class TopNode extends StatementBlockNode {
  /**
   * Creates a new, blank PHP source file.
   *
   * @param string|NULL $ns
   *  If provided, the new document will have this namespace added to it.
   *
   * @return static
   */
  public static function create($ns = NULL) {
    $node = new TopNode();
    $node->addChild(Token::openTag());
    if (is_string($ns) && $ns) {
      $ns = NamespaceNode::create($ns)->appendTo($node);
      WhitespaceNode::create("\n")->insertBefore($ns);
      WhitespaceNode::create("\n")->insertAfter($ns);
    }
    return $node;
  }
}
