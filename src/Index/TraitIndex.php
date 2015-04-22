<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

/**
 * Index information about a trait.
 */
class TraitIndex extends BaseIndex {

  /**
   * @var static[]
   */
  private $traits;

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
   * @param static[] $traits
   * @param PropertyIndex[] $properties
   * @param MethodIndex[] $methods
   */
  public function __construct(SourcePosition $position, $name, $traits, $properties, $methods) {
    parent::__construct($position, $name);
    $this->traits = $traits;
    $this->properties = $properties;
    $this->methods = $methods;
  }

  /**
   * Get traits used by this trait.
   *
   * @return PropertyIndex[]
   */
  public function getTraits() {
    return $this->traits;
  }

  /**
   * Get properties on this trait.
   *
   * @return PropertyIndex[]
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * Get methods on this trait.
   *
   * @return MethodIndex[]
   */
  public function getMethods() {
    return $this->methods;
  }

}
