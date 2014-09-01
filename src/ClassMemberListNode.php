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
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->childrenByInstance('\Pharborist\ClassMemberNode');
  }
}
