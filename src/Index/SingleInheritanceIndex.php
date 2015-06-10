<?php
namespace Pharborist\Index;

/**
 * Index information about a class.
 */
abstract class SingleInheritanceIndex extends BaseIndex {

  /**
   * @var string[]
   */
  protected $traits;

  /**
   * @var TraitPrecedenceIndex[]
   */
  protected $traitPrecedences;

  /**
   * @var TraitAliasIndex[]
   */
  protected $traitAliases;

  /**
   * @var PropertyIndex[]
   */
  protected $ownProperties;

  /**
   * @var MethodIndex[]
   */
  protected $ownMethods;

  /**
   * @var PropertyIndex[]
   */
  protected $traitProperties;

  /**
   * @var MethodIndex[]
   */
  protected $traitMethods;

  /**
   * @param FilePosition $position
   * @param string $name
   */
  public function __construct(FilePosition $position, $name) {
    parent::__construct($position, $name);
  }

  /**
   * Clear derived information from index.
   *
   * @internal Used by Indexer.
   *
   * @return $this
   */
  public function clear() {
    $this->traitProperties = NULL;
    $this->traitMethods = NULL;
    return $this;
  }

  /**
   * Get traits used by this class/trait.
   *
   * @return string[]
   */
  public function getTraits() {
    return $this->traits;
  }

  /**
   * Set traits used by this class/trait.
   *
   * @internal Used by Indexer.
   *
   * @param string[] $traits
   *   Fully qualified trait names.
   *
   * @return $this
   */
  public function setTraits($traits) {
    $this->traits = $traits;
    return $this;
  }

  /**
   * Get trait precedence rules for this class/trait.
   *
   * @return TraitPrecedenceIndex[]
   */
  public function getTraitPrecedences() {
    return $this->traitPrecedences;
  }

  /**
   * Set trait precedence rules for this class/trait.
   *
   * @internal Used by Indexer.
   *
   * @param TraitPrecedenceIndex[] $traitPrecedences
   *   Trait precendence rules.
   *
   * @return $this
   */
  public function setTraitPrecedences($traitPrecedences) {
    $this->traitPrecedences = $traitPrecedences;
    return $this;
  }

  /**
   * Get trait aliases for this class/trait.
   *
   * @return TraitAliasIndex[]
   */
  public function getTraitAliases() {
    return $this->traitAliases;
  }

  /**
   * Set trait aliases for this class/trait.
   *
   * @internal Used by Indexer.
   *
   * @param TraitAliasIndex[] $traitAliases
   *   Trait aliases.
   *
   * @return $this
   */
  public function setTraitAliases($traitAliases) {
    $this->traitAliases = $traitAliases;
    return $this;
  }

  /**
   * Get own properties on this class/trait.
   *
   * @return PropertyIndex[]
   */
  public function getOwnProperties() {
    return $this->ownProperties;
  }

  /**
   * Set own properties on this class/trait.
   *
   * @internal Used by Indexer.
   *
   * @param MethodIndex[] $ownProperties
   *   Properties declared on this class/trait.
   *
   * @return $this
   */
  public function setOwnProperties($ownProperties) {
    $this->ownProperties = $ownProperties;
    return $this;
  }

  /**
   * Get own methods on this class/trait.
   *
   * @return MethodIndex[]
   */
  public function getOwnMethods() {
    return $this->ownMethods;
  }

  /**
   * Set own methods on this class/trait.
   *
   * @internal Used by Indexer.
   *
   * @param MethodIndex[] $ownMethods
   *   Methods declared on this class/trait.
   *
   * @return $this
   */
  public function setOwnMethods($ownMethods) {
    $this->ownMethods = $ownMethods;
    return $this;
  }

  /**
   * Get properties on this class/trait from traits.
   *
   * @return PropertyIndex[]
   */
  public function getTraitProperties() {
    return $this->traitProperties;
  }

  /**
   * Set the trait properties.
   *
   * @internal Used by Indexer.
   *
   * @param PropertyIndex[] $traitProperties
   *   Properties from trait.
   *
   * @return $this
   */
  public function setTraitProperties($traitProperties) {
    $this->traitProperties = $traitProperties;
    return $this;
  }

  /**
   * Get methods on this class/trait from traits.
   *
   * @return MethodIndex[]
   */
  public function getTraitMethods() {
    return $this->traitMethods;
  }

  /**
   * Set the trait methods.
   *
   * @internal Used by Indexer.
   *
   * @param MethodIndex[] $traitMethods
   *   Methods from trait.
   *
   * @return $this
   */
  public function setTraitMethods($traitMethods) {
    $this->traitMethods = $traitMethods;
    return $this;
  }

  /**
   * Get properties on this class/trait.
   *
   * @return PropertyIndex[]
   */
  public function getProperties() {
    return array_merge($this->traitProperties, $this->ownProperties);
  }

  /**
   * Test if method exists.
   *
   * @param string $name
   * @return bool
   */
  public function hasMethod($name) {
    $methods = $this->getMethods();
    return isset($methods[$name]);
  }

  /**
   * Get methods on this class/trait.
   *
   * @return MethodIndex[]
   */
  public function getMethods() {
    return array_merge($this->traitMethods, $this->ownMethods);
  }

  /**
   * Get method.
   *
   * @param string $name
   * @return MethodIndex
   */
  public function getMethod($name) {
    $methods = $this->getMethods();
    return isset($methods[$name]) ? $methods[$name] : NULL;
  }
}
