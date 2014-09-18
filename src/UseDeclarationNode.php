<?php
namespace Pharborist;

/**
 * A use statement importing a class, function, or constant into a namespace.
 *
 * Example:
 * ```
 * use Cleese;
 * use Chapman as Palin;
 * ```
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

  /**
   * Name bounded inside namespace.
   *
   * @return string
   */
  public function getBoundedName() {
    if ($this->alias) {
      return $this->alias->getText();
    }
    else {
      return $this->name->lastChild()->getText();
    }
  }
}
