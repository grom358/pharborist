<?php
namespace Pharborist;

/**
 * A node tree visitor.
 */
interface VisitorInterface {
  /**
   * Called when visiting node.
   *
   * @param Node $node
   *   Node being visited.
   */
  public function visit(Node $node);

  /**
   * Called when completed visiting a parent node.
   *
   * @param ParentNode $node
   *   Parent node being visited.
   */
  public function visitEnd(ParentNode $node);
}
