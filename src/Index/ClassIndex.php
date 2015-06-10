<?php
namespace Pharborist\Index;

/**
 * Index information about a class.
 */
class ClassIndex extends SingleInheritanceIndex {

  /**
   * @var bool
   */
  protected $final;

  /**
   * @var bool
   */
  protected $abstract;

  /**
   * @var string
   */
  protected $extends;

  /**
   * @var string[]
   */
  protected $parents;

  /**
   * @var string[]
   */
  protected $implements;

  /**
   * @var string[]
   */
  protected $extendedBy;

  /**
   * @var ConstantIndex[]
   */
  protected $ownConstants;

  /**
   * @var ConstantIndex[]
   */
  protected $inheritedConstants;

  /**
   * @var PropertyIndex[]
   */
  protected $inheritedProperties;

  /**
   * @var MethodIndex[]
   */
  protected $inheritedMethods;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param bool $final
   * @param bool $abstract
   * @param string $extends
   * @param string[] $implements
   * @param ConstantIndex[] $ownConstants
   */
  public function __construct(
    FilePosition $position,
    $name,
    $final,
    $abstract,
    $extends,
    array $implements,
    array $ownConstants
  ) {
    parent::__construct($position, $name);
    $this->final = $final;
    $this->abstract = $abstract;
    $this->extends = $extends;
    $this->implements = $implements;
    $this->ownConstants = $ownConstants;
  }

  /**
   * Clear derived information from index.
   *
   * @internal Used by Indexer.
   *
   * @return $this
   */
  public function clear() {
    parent::clear();
    $this->inheritedConstants = [];
    $this->extendedBy = [];
    return $this;
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
   * Gets the class this extends.
   *
   * @return string
   */
  public function getExtends() {
    return $this->extends;
  }

  /**
   * Gets interfaces implemented by this class.
   *
   * @return string[]
   */
  public function getImplements() {
    return $this->implements;
  }

  /**
   * Get the parents of this class.
   *
   * @return string[]
   */
  public function getParents() {
    return $this->parents;
  }

  /**
   * Set the parents of this class.
   *
   * @internal Used by Indexer.
   *
   * @param string[] $parents
   *
   * @return $this
   */
  public function setParents($parents) {
    $this->parents = $parents;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getExtendedBy() {
    return $this->extendedBy;
  }

  /**
   * Add class as subclass of this class.
   *
   * @internal Used by Indexer.
   *
   * @param string $classFqn
   *   Fully qualified class name.
   *
   * @return $this
   */
  public function addSubclass($classFqn) {
    $this->extendedBy[] = $classFqn;
    return $this;
  }

  /**
   * @return ConstantIndex[]
   */
  public function getOwnConstants() {
    return $this->ownConstants;
  }

  /**
   * @return ConstantIndex[]
   */
  public function getInheritedConstants() {
    return $this->inheritedConstants;
  }

  /**
   * Set the inherited constants.
   *
   * @internal Used by Indexer.
   *
   * @param ConstantIndex[] $inheritedConstants
   *   Inherited constants from ancestors.
   *
   * @return $this
   */
  public function setInheritedConstants($inheritedConstants) {
    $this->inheritedConstants = $inheritedConstants;
    return $this;
  }

  /**
   * Get properties on this class/trait from parent class.
   *
   * @return PropertyIndex[]
   */
  public function getInheritedProperties() {
    return $this->inheritedProperties;
  }

  /**
   * Set the inherited properties.
   *
   * @internal Used by Indexer.
   *
   * @param PropertyIndex[] $inheritedProperties
   *   Inherited properties from ancestor classes.
   *
   * @return $this
   */
  public function setInheritedProperties($inheritedProperties) {
    $this->inheritedProperties = $inheritedProperties;
    return $this;
  }

  /**
   * Get methods on this class/trait from parent class.
   *
   * @return MethodIndex[]
   */
  public function getInheritedMethods() {
    return $this->inheritedMethods;
  }

  /**
   * Set the inherited methods.
   *
   * @internal Used by Indexer.
   *
   * @param MethodIndex[] $inheritedMethods
   *   Inherited methods from ancestor classes.
   *
   * @return $this
   */
  public function setInheritedMethods($inheritedMethods) {
    $this->inheritedMethods = $inheritedMethods;
    return $this;
  }

  /**
   * Get constants on this class.
   *
   * @return ConstantIndex[]
   */
  public function getConstants() {
    return array_merge($this->inheritedConstants, $this->ownConstants);
  }

  /**
   * Get properties on this class.
   *
   * @return PropertyIndex[]
   */
  public function getProperties() {
    return array_merge($this->inheritedProperties, $this->traitProperties, $this->ownProperties);
  }

  /**
   * Get methods on this class.
   *
   * @return MethodIndex[]
   */
  public function getMethods() {
    return array_merge($this->inheritedMethods, $this->traitMethods, $this->ownMethods);
  }

}
