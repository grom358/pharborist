<?php
namespace Pharborist;

/**
 * Namespace declaration.
 * @package Pharborist
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
