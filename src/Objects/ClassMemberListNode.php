<?php
namespace Pharborist\Objects;

use Pharborist\NodeCollection;
use Pharborist\TokenNode;
use Pharborist\Parser;
use Pharborist\DocCommentTrait;
use Pharborist\CommaListNode;
use Pharborist\Token;
use Pharborist\Types;

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
   * @return NodeCollection|ClassMemberNode[]
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

  /**
   * Get the type of the members as defined by doc comment.
   *
   * @return string[]
   *   The types as defined by phpdoc standard. Default is ['mixed'].
   */
  public function getTypes() {
    // No type specified means type is mixed.
    $types = ['mixed'];
    // Use types from the doc comment if available.
    $doc_comment = $this->getDocComment();
    if (!$doc_comment) {
      return $types;
    }
    $doc_block = $doc_comment->getDocBlock();
    $var_tags = $doc_block->getTagsByName('var');
    if (empty($var_tags)) {
      return $types;
    }
    /** @var \phpDocumentor\Reflection\DocBlock\Tag\VarTag $var_tag */
    $var_tag = reset($var_tags);
    return Types::normalize($var_tag->getTypes());
  }
}
