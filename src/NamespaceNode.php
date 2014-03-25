<?php
namespace Pharborist;

/**
 * Namespace declaration.
 */
class NamespaceNode extends StatementNode {
  /**
   * @var Node
   */
  public $name;

  /**
   * @var Node
   */
  public $body;
}
