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
    return isset($this->static) && $this->static->getType() === T_STATIC;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @param boolean $static
   *
   * @return $this
   */
  public function setStatic($static) {
    if ($static === TRUE && empty($this->static)) {
      // @todo Er...is it possible that there might not *be* a visibility
      // node here? If so, how to handle that?
      $this->static = Token::_static()->insertAfter($this->visibility);
      WhitespaceNode::create(' ')->insertBefore($this->static);
    }
    elseif ($static === FALSE && isset($this->static)) {
      $this->static->next()->remove();
      $this->static->remove();
      $this->static = NULL;
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
