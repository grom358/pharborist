<?php
namespace Pharborist;

/**
 * Top node of tree.
 */
class TopNode extends StatementBlockNode {
  /**
   * @return TopNode
   */
  public static function create() {
    $node = new TopNode();
    $node->addChild(Token::openTag());
    return $node;
  }
}
