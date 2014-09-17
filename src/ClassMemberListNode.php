<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends ClassStatementNode {
  use DocCommentTrait;
  use VisibilityTrait;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @param string $property
   *   Property name.
   * @return ClassMemberListNode
   */
  public static function create($property) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Property {private \${$property};}");
    $property = $class_node->getBody()->firstChild()->remove();
    return $property;
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
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->childrenByInstance('\Pharborist\ClassMemberNode');
  }
}
