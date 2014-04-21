<?php
namespace Pharborist;

/**
 * Use declaration.
 */
class UseDeclarationNode extends ParentNode {
  /**
   * @var NameNode
   */
  protected $name;

  /**
   * @var TokenNode
   */
  protected $alias;

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getAlias() {
    return $this->alias;
  }
}
