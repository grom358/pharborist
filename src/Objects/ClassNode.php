<?php
namespace Pharborist\Objects;

use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\NodeCollection;
use Pharborist\Parser;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * Class declaration.
 */
class ClassNode extends SingleInheritanceNode {

  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @param $class_name
   * @return ClassNode
   */
  public static function create($class_name) {
    $class_node = Parser::parseSnippet("class $class_name {}")->remove();
    return $class_node;
  }

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->abstract;
  }

  /**
   * @param boolean $is_abstract
   * @return $this
   */
  public function setAbstract($is_abstract) {
    if ($is_abstract) {
      if (!isset($this->abstract)) {
        $this->abstract = Token::_abstract();
        $this->prepend([
          $this->abstract,
          Token::space(),
        ]);
        $this->setFinal(FALSE);
      }
    }
    else {
      if (isset($this->abstract)) {
        // Remove whitespace.
        $this->abstract->next()->remove();
        // Remove abstract.
        $this->abstract->remove();
      }
    }
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }

  /**
   * @param boolean $is_final
   * @return $this
   */
  public function setFinal($is_final) {
    if ($is_final) {
      if (!isset($this->final)) {
        $this->final = Token::_final();
        $this->prepend([
          $this->final,
          Token::space(),
        ]);
        $this->setAbstract(FALSE);
      }
    }
    else {
      if (isset($this->final)) {
        // Remove whitespace.
        $this->final->next()->remove();
        // Remove final.
        $this->final->remove();
      }
    }
    return $this;
  }

  /**
   * Returns the names of all class properties, regardless of visibility.
   *
   * @return string[]
   */
  public function getPropertyNames() {
    return array_map(function(ClassMemberNode $property) {
      return ltrim($property->getName(), '$');
    }, $this->getAllProperties()->toArray());
  }

  /**
   * Returns the names of all class methods, regardless of visibility.
   *
   * @return string[]
   */
  public function getMethodNames() {
    return array_map(function(ClassMethodNode $node) {
      return $node->getName()->getText();
    }, $this->getAllMethods()->toArray());
  }

  /**
   * Returns if the class has the named property, regardless of visibility.
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
   * @return \Pharborist\NodeCollection
   */
  public function getAllProperties() {
    $properties = [];
    /** @var ClassMemberListNode $node */
    foreach ($this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\ClassMemberListNode')) as $node) {
      $properties = array_merge($properties, $node->getMembers());
    }
    return new NodeCollection($properties);
  }

  /**
   * @return \Pharborist\NodeCollection
   */
  public function getAllMethods() {
    return $this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\ClassMethodNode'));
  }

  /**
   * Returns a property by name, if it exists.
   *
   * @param string $name
   *  The property name, with or without the $.
   *
   * @return \Pharborist\Objects\ClassMemberNode|NULL
   */
  public function getProperty($name) {
    $name = ltrim($name, '$');

    $properties = $this
      ->getAllProperties()
      ->filter(function(ClassMemberNode $property) use ($name) {
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
   * @return \Pharborist\Objects\ClassMethodNode|NULL
   */
  public function getMethod($name) {
    $methods = $this
      ->getAllMethods()
      ->filter(function(ClassMethodNode $method) use ($name) {
        return $method->getName()->getText() === $name;
      });
    return $methods->isEmpty() ? NULL : $methods[0];
  }

  /**
   * Creates a new property in this class.
   *
   * @see ClassMemberNode::create
   * @return $this
   */
  public function createProperty($name, ExpressionNode $value = NULL, $visibility = 'public') {
    return $this->appendProperty(ClassMemberNode::create($name, $value, $visibility));
  }
}
