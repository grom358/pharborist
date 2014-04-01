<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  public $docComment;

  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $body;
}
