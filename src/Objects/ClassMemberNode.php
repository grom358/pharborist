<?php
namespace Pharborist\Objects;

use Pharborist\ExpressionNode;
use Pharborist\Node;
use Pharborist\ParentNode;
use Pharborist\Parser;
use Pharborist\TokenNode;

/**
 * A single class member in a ClassMemberListNode.
 *
 * The relationship between class members and class member lists can be
 * a bit confusing. Both of these are considered class member lists:
 * ```
 * protected $foo;  // A member list with one member.
 * private $bar, $baz;
 * ```
 * The individual members in those lists are $foo, $bar, and $baz. Each of
 * them is a ClassMemberNode, which will render as `$foo`, `$bar`, and `$baz`,
 * respectively. And each is a child of a parent ClassMemberListNode, which
 * has a visibility and static-ness.
 *
 * ClassMemberNode's getVisibility(), setVisibility(), and is/get/setStatic()
 * methods are actually convenience methods which call the same method on the
 * parent member list. But the visibility and static keywords are still
 * attributes of the *list*, not the individual member.
 *
 * @see ClassMemberListNode
 */
class ClassMemberNode extends ParentNode {
  /**
   * @var Node
   */
  protected $name;

  /**
   * @var Node
   */
  protected $value;

  /**
   * Creates a new class member.
   *
   * @param string $name
   *  The name of the member, with or without the leading $.
   * @param \Pharborist\ExpressionNode $value
   *  The default value of the member, if any.
   * @param string $visibility
   *  The member's visibility. Can be public, private, or protected. Defaults to
   *  public.
   *
   * @return ClassMemberListNode
   *
   * @todo Not all expressions can be default values, but I forget what sorts of
   * expressions are valid for this. Will need better sanity checking here.
   */
  public static function create($name, ExpressionNode $value = NULL, $visibility = 'public') {
    $code = $visibility . ' $' . ltrim($name, '$');
    if ($value instanceof ExpressionNode) {
      $code .= ' = ' . $value->getText();
    }
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet('class Foo { ' . $code . '; }');
    return $class_node->getStatements()[0]->remove();
  }

  /**
   * @return Node
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Node
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * @return ClassMemberListNode
   */
  public function getClassMemberListNode() {
    return $this->parent()->parent();
  }

  /**
   * @see \Pharborist\ClassMemberListNode::isStatic()
   *
   * @return boolean
   */
  public function isStatic() {
    return $this->getClassMemberListNode()->isStatic();
  }

  /**
   * @see \Pharborist\ClassMemberListNode::getStatic()
   *
   * @return \Pharborist\TokenNode
   */
  public function getStatic() {
    return $this->getClassMemberListNode()->getStatic();
  }

  /**
   * @see \Pharborist\ClassMemberListNode::setStatic()
   *
   * @param boolean $is_static
   *
   * @return $this
   */
  public function setStatic($is_static) {
    $this->getClassMemberListNode()->setStatic($is_static);
    return $this;
  }

  /**
   * @see \Pharborist\VisibilityTrait::getVisibility()
   *
   * @return \Pharborist\TokenNode
   */
  public function getVisibility() {
    return $this->getClassMemberListNode()->getVisibility();
  }

  /**
   * @see \Pharborist\VisibilityTrait::setVisibility()
   *
   * @param string|integer|TokenNode|NULL $visibility
   *
   * @return $this
   */
  public function setVisibility($visibility) {
    $this->getClassMemberListNode()->setVisibility($visibility);
    return $this;
  }

  /**
   * Get the type of the member as defined by doc comment.
   *
   * @return string[]
   *   The types as defined by phpdoc standard. Default is ['mixed'].
   */
  public function getTypes() {
    return $this->getClassMemberListNode()->getTypes();
  }
}
