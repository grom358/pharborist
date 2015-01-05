<?php
namespace Pharborist\Namespaces;

use Pharborist\ParentNode;
use Pharborist\TokenNode;
use Pharborist\Filter;
use Pharborist\WhitespaceNode;
use Pharborist\Token;
use Pharborist\Node;
use Pharborist\Parser;

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

  /**
   * @param string $import
   *   Fully qualified class name; can also include optional alias.
   *
   * @return UseDeclarationNode
   */
  public static function create($import) {
    /** @var UseDeclarationBlockNode $use_declaration_block_node */
    $use_declaration_block_node = Parser::parseSnippet('use ' . $import . ';');
    return $use_declaration_block_node->getDeclarationStatements()[0]->getDeclarations()[0];
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
      $this->alias->previousUntil(Filter::isInstanceOf('\Pharborist\Namespaces\NameNode'))->remove();
      $this->alias->remove();
      $this->alias = NULL;
    }
    else {
      throw new \InvalidArgumentException();
    }

    return $this;
  }

  /**
   * Test if use declaration is for class.
   *
   * @return bool
   *   TRUE if use declaration of class.
   */
  public function isClass() {
    /** @var UseDeclarationStatementNode $parent */
    $parent = $this->parent->parent;
    return $parent->importsClass();
  }

  /**
   * Test if use declaration is for function.
   *
   * @return bool
   *   TRUE if use declaration of function.
   */
  public function isFunction() {
    /** @var UseDeclarationStatementNode $parent */
    $parent = $this->parent->parent;
    return $parent->importsFunction();
  }

  /**
   * Test if use declaration is for const.
   *
   * @return bool
   *   TRUE if use declaration of const.
   */
  public function isConst() {
    /** @var UseDeclarationStatementNode $parent */
    $parent = $this->parent->parent;
    return $parent->importsConst();
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
