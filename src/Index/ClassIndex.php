<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

/**
 * Index information about a class.
 */
class ClassIndex extends BaseIndex {

  /**
   * @var bool
   */
  private $final;

  /**
   * @var bool
   */
  private $abstract;

  /**
   * @var TraitIndex[]
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
   * @param bool $final
   * @param bool $abstract
   * @param PropertyIndex[] $properties
   * @param MethodIndex[] $methods
   */
  public function __construct(SourcePosition $position, $name, $final, $abstract, $traits, $properties, $methods) {
    parent::__construct($position, $name);
    $this->final = $final;
    $this->abstract = $abstract;
    $this->traits = $traits;
    $this->properties = $properties;
    $this->methods = $methods;
  }

  /**
   * Class is final.
   *
   * @return bool
   */
  public function isFinal() {
    return $this->final;
  }

  /**
   * Class is abstract.
   *
   * @return bool
   */
  public function isAbstract() {
    return $this->abstract;
  }

  /**
   * Get traits used by this class.
   *
   * @return TraitIndex[]
   */
  public function getTraits() {
    return $this->traits;
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
