<?php
namespace Pharborist;

/**
 * Public API for Node.
 *
 * This interface is used by tagging interfaces such as ExpressionNode to
 * indicate they support the Node API.
 */
interface NodeInterface {
  /**
   * Get the parent node.
   * @return ParentNode
   */
  public function getParent();

  /**
   * Get the ancestors of this node.
   * @return ParentNode[]
   */
  public function getAncestors();

  /**
   * Get the ancestor of given type.
   * @param string $type
   * @return ParentNode
   */
  public function getAncestor($type);

  /**
   * Insert a node before this node.
   * @param Node $node Node to insert.
   * @return $this
   */
  public function insertBefore(Node $node);

  /**
   * Insert a node after this node.
   * @param Node $node Node to insert.
   * @return $this
   */
  public function insertAfter(Node $node);

  /**
   * Remove node from its parent.
   * @return $this
   */
  public function remove();

  /**
   * Replace this node in its parent.
   * @param Node $node Replacement node.
   * @return $this
   */
  public function replace(Node $node);

  /**
   * Get the previous sibling.
   * @return Node
   */
  public function previousSibling();

  /**
   * Get the next sibling.
   * @return Node
   */
  public function nextSibling();

  /**
   * Get the source position of the node.
   * @return SourcePosition
   */
  public function getSourcePosition();
}
