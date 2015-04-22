<?php
namespace Pharborist\Index;

use Pharborist\SourcePosition;

/**
 * Index information about a class.
 */
class ClassIndex extends BaseIndex {

  use ConstantContainerTrait;
  use MethodContainerTrait;
  use PropertyContainerTrait;
  use TraitConsumerTrait;

  /**
   * @var bool
   */
  private $final;

  /**
   * @var bool
   */
  private $abstract;

  /**
   * @var InterfaceIndex[]
   */
  private $interfaces;

  /**
   * @param SourcePosition $position
   * @param string $name
   * @param bool $final
   * @param bool $abstract
   * @param InterfaceIndex[] $interfaces
   * @param PropertyIndex[] $properties
   * @param MethodIndex[] $methods
   */
  public function __construct(SourcePosition $position, $name, $final, $abstract, $interfaces, $properties, $methods) {
    parent::__construct($position, $name);
    $this->final = $final;
    $this->abstract = $abstract;
    $this->interfaces = $interfaces;
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
   * Gets interfaces implemented by this class.
   *
   * @return InterfaceIndex[]
   */
  public function getInterfaces() {
    return $this->interfaces;
  }

}
