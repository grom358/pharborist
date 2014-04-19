<?php
namespace Pharborist;

/**
 * Use declaration.
 */
class UseDeclarationNode extends ParentNode {
  /**
   * @var NamespacePathNode
   */
  protected $namespacePath;

  /**
   * @var TokenNode
   */
  protected $alias;

  /**
   * @return NamespacePathNode
   */
  public function getNamespacePath() {
    return $this->namespacePath;
  }

  /**
   * @return Node
   */
  public function getAlias() {
    return $this->alias;
  }
}
