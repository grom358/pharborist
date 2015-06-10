<?php
namespace Pharborist\Objects;

use Pharborist\DocCommentTrait;
use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\FormatterFactory;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\Namespaces\NameNode;
use Pharborist\NodeCollection;
use Pharborist\StatementBlockNode;
use Pharborist\StatementNode;

/**
 * Base class for ClassNode and TraitNode.
 *
 * @see ClassNode
 * @see TraitNode
 */
abstract class SingleInheritanceNode extends StatementNode {
  use DocCommentTrait;
  use IdentifierNameTrait;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->statements;
  }

  /**
   * @return NodeCollection|ClassStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }

  /**
   * Adds a method to a class/trait.
   *
   * @param \Pharborist\Functions\FunctionDeclarationNode|\Pharborist\Objects\ClassMethodNode|string $method
   *  The method to append. Can either be an existing method, a function (which
   *  will be converted to a public method), or a string (a new public method
   *  will be created with that name).
   *
   * @return $this
   */
  public function appendMethod($method) {
    if ($method instanceof FunctionDeclarationNode) {
      $method = ClassMethodNode::fromFunction($method);
    }
    elseif (is_string($method)) {
      $method = ClassMethodNode::create($method);
    }
    $this->statements->lastChild()->before($method);
    FormatterFactory::format($this);
    return $this;
  }

  /**
   * Returns if the class/trait has the named property, regardless of
   * visibility.
   *
   * @param string $name
   *  The property name, with or without a leading $.
   *
   * @return boolean
   */
  public function hasProperty($name) {
    return in_array(ltrim($name, '$'), $this->getPropertyNames());
  }

  /**
   * Returns the names of all class/trait properties, regardless of visibility.
   *
   * @return string[]
   */
  public function getPropertyNames() {
    return array_map(function (ClassMemberNode $property) {
      return ltrim($property->getName(), '$');
    }, $this->getProperties()->toArray());
  }

  /**
   * @return \Pharborist\NodeCollection|ClassMemberNode[]
   */
  public function getProperties() {
    $properties = [];
    /** @var ClassMemberListNode $node */
    foreach ($this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\ClassMemberListNode')) as $node) {
      $properties = array_merge($properties, $node->getMembers()->toArray());
    }
    return new NodeCollection($properties, FALSE);
  }

  /**
   * Returns if the class has the named method, regardless of visibility.
   *
   * @param string $name
   *  The method name.
   *
   * @return boolean
   */
  public function hasMethod($name) {
    return in_array($name, $this->getMethodNames());
  }

  /**
   * Returns the names of all class methods, regardless of visibility.
   *
   * @return string[]
   */
  public function getMethodNames() {
    return array_map(function (ClassMethodNode $node) {
      return $node->getName()->getText();
    }, $this->getMethods()->toArray());
  }

  /**
   * @return NodeCollection|ClassMethodNode[]
   */
  public function getMethods() {
    return $this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\ClassMethodNode'));
  }

  /**
   * @return NodeCollection|TraitUseNode[]
   */
  public function getTraitUses() {
    return $this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\TraitUseNode'));
  }

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getTraits() {
    $traits = [];
    /** @var TraitUseNode $node */
    foreach ($this->getTraitUses() as $node) {
      $traits = array_merge($traits, $node->getTraits()->toArray());
    }
    return new NodeCollection($traits, FALSE);
  }

  /**
   * Returns a property by name, if it exists.
   *
   * @param string $name
   *  The property name, with or without the $.
   *
   * @return ClassMemberNode|NULL
   */
  public function getProperty($name) {
    $name = ltrim($name, '$');

    $properties = $this
      ->getProperties()
      ->filter(function (ClassMemberNode $property) use ($name) {
        return ltrim($property->getName(), '$') === $name;
      });
    return $properties->isEmpty() ? NULL : $properties[0];
  }

  /**
   * Returns a method by name, if it exists.
   *
   * @param string $name
   *  The method name.
   *
   * @return ClassMethodNode|NULL
   */
  public function getMethod($name) {
    $methods = $this
      ->getMethods()
      ->filter(function (ClassMethodNode $method) use ($name) {
        return $method->getName()->getText() === $name;
      });
    return $methods->isEmpty() ? NULL : $methods[0];
  }

  /**
   * Creates a new property in this class.
   *
   * @see ClassMemberNode::create
   *
   * @param string $name
   * @param ExpressionNode $value
   * @param string $visibility
   *
   * @return $this
   */
  public function createProperty($name, ExpressionNode $value = NULL, $visibility = 'public') {
    return $this->appendProperty(ClassMemberNode::create($name, $value, $visibility));
  }

  /**
   * Add property to class.
   *
   * @param string|ClassMemberListNode $property
   * @return $this
   */
  public function appendProperty($property) {
    if (is_string($property)) {
      $property = ClassMemberListNode::create($property);
    }
    $properties = $this->statements->children(Filter::isInstanceOf('\Pharborist\ClassMemberListNode'));
    if ($properties->count() === 0) {
      $this->statements->firstChild()->after($property);
    }
    else {
      $properties->last()->after($property);
    }
    FormatterFactory::format($this);
    return $this;
  }
}
