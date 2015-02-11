<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

/**
 * Index information about a class.
 */
class ClassIndex extends BaseIndex {

  /**
   * @var string
   */
  private $name;

  /**
   * @var PropertyIndex[]
   */
  private $properties;

  /**
   * @var MethodIndex[]
   */
  private $methods;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param PropertyIndex[] $properties
   * @param MethodIndex[] $methods
   */
  public function __construct($position, $name, $properties, $methods) {
    $this->sourcePosition = $position;
    $this->name = $name;
    $this->properties = $properties;
    $this->methods = $methods;
  }

  /**
   * Get the fully qualified name of the class.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Get properties on this class.
   *
   * @return PropertyIndex[]
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * Get methods on this class.
   *
   * @return MethodIndex[]
   */
  public function getMethods() {
    return $this->methods;
  }

}
