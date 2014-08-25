<?php
namespace Pharborist;

/**
 * A class member list declaration.
 */
class ClassMemberListNode extends ClassStatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $static;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @param string $property
   *   Property with visibility modifier.
   * @return ClassMemberListNode
   */
  public static function create($property) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Property {{$property};}");
    $property = $class_node->getBody()->firstChild()->remove();
    return $property;
  }

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @return ClassMemberNode[]
   */
  public function getMembers() {
    return $this->childrenByInstance('\Pharborist\ClassMemberNode');
  }
}
