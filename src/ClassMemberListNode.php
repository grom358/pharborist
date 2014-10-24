<?php
namespace Pharborist;

/**
 * A class member list declaration, e.g. `protected $foo, $bar;` Even if you define
 * a single member per declaration, it's still considered a list.
 */
class ClassMemberListNode extends ClassStatementNode {
  use DocCommentTrait;
  use VisibilityTrait;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @var CommaListNode
   */
  protected $members;

  /**
   * @param string $property
   *   Property name.
   * @return ClassMemberListNode
   */
  public static function create($property) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Property {private \${$property};}");
    $property = $class_node->getStatements()[0]->remove();
    return $property;
  }

  /**
   * Remove the visibility modifier.
   */
  protected function removeVisibility() {
    throw new \BadMethodCallException("Can not remove visibility from class property.");
  }

  /**
   * @return boolean
   */
  public function isStatic() {
    return isset($this->static);
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @param boolean $is_static
   *
   * @return $this
   */
  public function setStatic($is_static) {
    if ($is_static) {
      if (!isset($this->static)) {
        $this->static = Token::_static();
        $this->visibility->after([Token::space(), $this->static]);
      }
    }
    else {
      if (isset($this->static)) {
        // Remove whitespace after static keyword.
        $this->static->next()->remove();
        // Remove static keyword.
        $this->static->remove();
      }
    }
    return $this;
  }

  /**
   * @return CommaListNode
   */
  public function getMemberList() {
    return $this->members;
  }

  /**
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->members->getItems();
  }

  /**
   * Adds this property list to a class, detaching it from its current
   * parent.
   *
   * @param ClassNode $class
   *  The target class.
   *
   * @return $this
   */
  public function addTo(ClassNode $class) {
    $class->appendProperty($this->remove());
    return $this;
  }

  /**
   * Creates a clone of this property list and adds it to a class.
   *
   * @param ClassNode $class
   *  The target class.
   *
   * @return static
   *  The cloned property list.
   */
  public function cloneInto(ClassNode $class) {
    $clone = clone $this;
    $class->appendProperty($this);
    return $clone;
  }
}
