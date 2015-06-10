<?php
namespace Pharborist\Index;

/**
 * Index information about an interface
 */
class InterfaceIndex extends BaseIndex {

  /**
   * @var string[]
   */
  protected $extends;

  /**
   * @var ConstantIndex[]
   */
  protected $ownConstants;

  /**
   * @var ConstantIndex[]
   */
  protected $inheritedConstants;

  /**
   * @var MethodIndex[]
   */
  protected $ownMethods;

  /**
   * @var MethodIndex[]
   */
  protected $inheritedMethods;

  /**
   * @var string[]
   */
  protected $extendedBy;

  /**
   * @var string[]
   */
  protected $implementedBy;

  /**
   * @param FilePosition $position
   * @param string $name
   * @param string[] $extends
   * @param ConstantIndex[] $ownConstants
   * @param MethodIndex[] $ownMethods
   */
  public function __construct(
    FilePosition $position,
    $name,
    $extends,
    array $ownConstants,
    array $ownMethods
  ) {
    parent::__construct($position, $name);
    $this->extends = $extends;
    $this->ownConstants = $ownConstants;
    $this->ownMethods = $ownMethods;
  }

  /**
   * Clear derived information from index.
   *
   * @internal Used by Indexer.
   *
   * @return $this
   */
  public function clear() {
    $this->extendedBy = [];
    $this->implementedBy = [];
    $this->inheritedConstants = NULL;
    $this->inheritedMethods = NULL;
    return $this;
  }

  /**
   * Get extended interfaces.
   *
   * @return string[]
   */
  public function getExtends() {
    return $this->extends;
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
   *   Inherited constants from ancestor interfaces.
   *
   * @return $this
   */
  public function setInheritedConstants($inheritedConstants) {
    $this->inheritedConstants = $inheritedConstants;
    return $this;
  }

  /**
   * @return ConstantIndex[]
   */
  public function getConstants() {
    return array_merge($this->inheritedConstants, $this->ownConstants);
  }

  /**
   * Get methods defined by this interface.
   *
   * @return MethodIndex[]
   */
  public function getOwnMethods() {
    return $this->ownMethods;
  }

  /**
   * Get methods inherited by this interface.
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
   *   Inherited methods from ancestor interfaces.
   *
   * @return $this
   */
  public function setInheritedMethods($inheritedMethods) {
    $this->inheritedMethods = $inheritedMethods;
    return $this;
  }

  /**
   * Get all methods on this interface.
   *
   * @return MethodIndex[]
   */
  public function getMethods() {
    return array_merge($this->inheritedMethods, $this->ownMethods);
  }

  /**
   * @return string[]
   */
  public function getExtendedBy() {
    return $this->extendedBy;
  }

  /**
   * Add interface as child of this interface.
   *
   * @internal Used by Indexer.
   *
   * @param string $interface_fqn
   *   Fully qualified interface name.
   *
   * @return $this
   */
  public function addExtendedBy($interface_fqn) {
    $this->extendedBy[] = $interface_fqn;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getImplementedBy() {
    return $this->implementedBy;
  }

  /**
   * Add class as implementation of interface.
   *
   * @internal Used by Indexer.
   *
   * @param string $class_fqn
   *   Fully qualified class name.
   *
   * @return $this
   */
  public function addImplementedBy($class_fqn) {
    $this->implementedBy[] = $class_fqn;
    return $this;
  }

}
