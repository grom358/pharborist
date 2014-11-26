<?php
namespace Pharborist;

/**
 * A node tree visitor.
 */
interface VisitorInterface {
  /**
   * Called when first reach parent node.
   *
   * @param ParentNode $node
   *   Parent node being visited.
   */
  public function visitBegin(ParentNode $node);

  /**
   * Called when visited node.
   *
   * @param Node $node
   *   Node being visited.
   */
  public function visitChild(Node $node);

  /**
   * Called when completed visiting a parent node.
   *
   * @param ParentNode $node
   *   Parent node being visited.
   */
  public function visitEnd(ParentNode $node);
}
