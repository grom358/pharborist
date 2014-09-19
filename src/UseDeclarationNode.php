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

  public static function create($import) {
    return Parser::parseSnippet('use ' . $import . ';')->firstChild();
  }

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
