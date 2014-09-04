<?php
namespace Pharborist;

/**
 * Trait for nodes that have parameters. For example, function declarations.
 */
trait ParameterTrait {
  /**
   * @var CommaListNode
   */
  protected $parameters;

  /**
   * @return CommaListNode
   */
  public function getParameterList() {
    return $this->parameters;
  }

  /**
   * @return ParameterNode[]
   */
  public function getParameters() {
    return $this->parameters->getItems();
  }

  /**
   * @return string[]
   */
  public function getParameterNames() {
    return array_map(function(ParameterNode $parameter) {
      return substr($parameter->getName(), 1);
    }, $this->getParameters());
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function prependParameter(ParameterNode $parameter) {
    $this->parameters->prependItem($parameter);
    return $this;
  }

  /**
   * @param ParameterNode $parameter
   * @return $this
   */
  public function appendParameter(ParameterNode $parameter) {
    $this->parameters->appendItem($parameter);
    return $this;
  }

  /**
   * Insert parameter before parameter at index.
   *
   * @param ParameterNode $parameter
   * @param int $index
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   * @return $this
   */
  public function insertParameter(ParameterNode $parameter, $index) {
    $this->parameters->insertItem($parameter, $index);
    return $this;
  }

  /**
   * Remove all parameters.
   *
   * @return $this
   */
  public function clearParameters() {
    $this->parameters->clear();
    return $this;
  }

  /**
   * Gets a parameter by name or index.
   *
   * @param mixed $key
   *  The parameter's name (without leading $) or position in the
   *  parameter list.
   *
   * @return ParameterNode
   *
   * @throws \InvalidArgumentException if the key is not a string or integer.
   */
  public function getParameter($key) {
    if (is_string($key)) {
      return $this->getParameterByName($key);
    }
    elseif (is_integer($key)) {
      return $this->getParameterAtIndex($key);
    }
    else {
      throw new \InvalidArgumentException("Illegal parameter index {$key}.");
    }
  }

  /**
   * Gets a parameter by its position in the parameter list.
   *
   * @param integer $index
   *
   * @return ParameterNode
   */
  public function getParameterAtIndex($index) {
    return $this->getParameterList()->getItem($index);
  }

  /**
   * Gets a parameter by its name.
   *
   * @param string $name
   *  The parameter name without leading $.
   *
   * @return ParameterNode
   *
   * @throws \UnexpectedValueException if the named parameter doesn't exist.
   */
  public function getParameterByName($name) {
    foreach ($this->getParameters() as $parameter) {
      // @todo Change this when #66 is merged
      if ($parameter->getName() === '$' . $name) {
        return $parameter;
      }
    }
    throw new \UnexpectedValueException("Parameter {$name} does not exist.");
  }

  /**
   * @return boolean
   */
  public function hasRequiredParameters() {
    return ($this->getRequiredParameters()->count() > 0);
  }
  
  /**
   * @return NodeCollection
   */
  public function getRequiredParameters() {
    return $this->parameters
      ->children(Filter::isInstanceOf('Pharborist\ParameterNode'))
      ->filter(function(ParameterNode $parameter) {
        $value = $parameter->getValue();
        return !isset($value);
      });
  }
  
  /**
   * @return NodeCollection
   */
  public function getOptionalParameters() {
    return $this->parameters
      ->children(Filter::isInstanceOf('Pharborist\ParameterNode'))
      ->filter(function(ParameterNode $parameter) {
        $value = $parameter->getValue();
        return isset($value);
      });
  }
}
