<?php
namespace Pharborist;

/**
 * A use declaration importing a class, function, or constant into a namespace.
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
   * @return boolean
   */
  public function hasAlias() {
    return isset($this->alias);
  }

  /**
   * @return Node
   */
  public function getAlias() {
    return $this->alias;
  }

  /**
   * Sets the imported item's alias. If NULL is passed, the alias is removed.
   *
   * @param \Pharborist\TokenNode|string|NULL $alias
   *
   * @return $this
   */
  public function setAlias($alias) {
    if (is_string($alias)) {
      $alias = new TokenNode(T_STRING, $alias);
    }

    if ($alias instanceof TokenNode) {
      if ($this->hasAlias()) {
        $this->alias->replaceWith($alias);
      }
      else {
        $this->alias = $alias;
        $this->addChild(WhitespaceNode::create(' '));
        $this->addChild(Token::_as());
        $this->addChild(WhitespaceNode::create(' '));
        $this->addChild($alias, 'alias');
      }
    }
    elseif ($alias === NULL && $this->hasAlias()) {
      $this->alias->previousUntil(Filter::isInstanceOf('\Pharborist\NameNode'))->remove();
      $this->alias->remove();
      $this->alias = NULL;
    }
    else {
      throw new \InvalidArgumentException();
    }

    return $this;
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
