<?php
namespace Pharborist\Builder;

class ClassBuilder {
  /**
   * @var string
   */
  protected $className;

  /**
   * @var boolean
   */
  protected $abstract;

  /**
   * @var boolean
   */
  protected $final;

  /**
   * @var string
   */
  protected $extends;

  /**
   * @var array
   */
  protected $implements;

  /**
   * @var array
   */
  protected $properties;

  /**
   * @var array
   */
  protected $methods;

  public function __construct($className) {
    $this->className = $className;
    $this->properties = [];
    $this->methods = [];
  }

  public function setAbstract($abstract) {
    $this->abstract = $abstract;
    $this->checkClassModifiers();
    return $this;
  }

  public function setFinal($final) {
    $this->final = $final;
    $this->checkClassModifiers();
    return $this;
  }

  protected function checkClassModifiers() {
    if ($this->final && $this->abstract) {
      throw new \Exception("Class can not be both abstract and final");
    }
  }

  public function setExtends($extends) {
    $this->extends = $extends;
    return $this;
  }

  public function setImplements($implements) {
    if (is_string($implements)) {
      $implements = [$implements];
    }
    if (!is_array($implements)) {
      throw new \InvalidArgumentException("Invalid implement arguments");
    }
    $this->implements = $implements;
  }

  public function addProperty($propertyName) {
    $propertyBuilder = new PropertyBuilder($propertyName);
    $this->properties[] = $propertyBuilder;
    return $propertyBuilder;
  }

  public function addMethod($methodName) {
    $methodBuilder = new MethodBuilder($methodName);
    $this->methods[] = $methodBuilder;
    return $methodBuilder;
  }

  public function __toString() {
    $template = '';
    $template .= 'class ' . $this->className . ' ';
    if ($this->extends) {
      $template .= 'extends ' . $this->extends . ' ';
    }
    if ($this->implements) {
      $template .= 'implements ' . implode(', ', $this->implements) . ' ';
    }
    $template .= '{';
    foreach ($this->properties as $property) {
      $template .= "\n" . str_repeat(' ', 2) . $property . "\n";
    }
    foreach ($this->methods as $method) {
      $template .= "\n" . str_repeat(' ', 2) . $method . "\n";
    }
    if (empty($this->properties) && empty($this->methods)) {
      $template .= "\n";
    }
    $template .= "}\n";
    return $template;
  }
}
