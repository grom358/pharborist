<?php
namespace Pharborist\Functions;

use Pharborist\Filter;
use Pharborist\NodeCollection;
use Pharborist\CommaListNode;

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
   * @return ParameterNodeCollection
   */
  public function getParameters() {
    $parameters = $this->parameters->getItems()->toArray();
    return new ParameterNodeCollection($parameters, FALSE);
  }

  /**
   * @return string[]
   */
  public function getParameterNames() {
    return array_map(function(ParameterNode $parameter) {
      return $parameter->getName();
    }, $this->getParameters()->toArray());
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
   * Appends a parameter.
   *
   * @param \Pharborist\Functions\ParameterNode|callable $parameter
   *  Either an existing parameter node, or a callable which will return
   *  the parameter to append. The callable will receive $this as its
   *  only argument.
   *
   * @return $this
   *
   * @throws \InvalidArgumentException
   */
  public function appendParameter($parameter) {
    if (is_callable($parameter)) {
      $parameter = $parameter($this);
    }
    if (!($parameter instanceof ParameterNode)) {
      throw new \InvalidArgumentException();
    }
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
   *  The parameter name with or without leading $.
   *
   * @return ParameterNode
   *
   * @throws \UnexpectedValueException if the named parameter doesn't exist.
   */
  public function getParameterByName($name) {
    $name = ltrim($name, '$');
    /** @var ParameterNode $parameter */
    foreach ($this->getParameters()->reverse() as $parameter) {
      if ($parameter->getName() === $name) {
        return $parameter;
      }
    }
    throw new \UnexpectedValueException("Parameter {$name} does not exist.");
  }

  /**
   * Checks if the function/method has a certain parameter.
   *
   * @param mixed $parameter
   *  Either the parameter name (with or without the $), or a ParameterNode.
   * @param string $type
   *  Optional type hint to check as well.
   *
   * @return boolean
   *
   * @throws \InvalidArgumentException if $parameter is neither a string or
   * a ParameterNode.
   */
  public function hasParameter($parameter, $type = NULL) {
    if (is_string($parameter)) {
      try {
        $parameter = $this->getParameterByName($parameter);
      }
      catch (\UnexpectedValueException $e) {
        return FALSE;
      }
    }
    if (!($parameter instanceof ParameterNode)) {
      throw new \InvalidArgumentException();
    }
    if ($parameter->parent() !== $this->parameters) {
      return FALSE;
    }
    if ($type === NULL) {
      return TRUE;
    }
    else {
      return $parameter->getTypeHint()->getText() === $type;
    }
  }

  /**
   * Checks if the function/method has a specific required parameter.
   *
   * @param mixed $parameter
   *  Either the name of the parameter (with or without leading $), or a
   *  ParameterNode.
   * @param string $type
   *  Optional type hint to check.
   *
   * @return boolean
   */
  public function hasRequiredParameter($parameter, $type = NULL) {
    return $this->hasParameter($parameter, $type) && $this->getParameterByName($parameter)->isRequired();
  }

  /**
   * Checks if the function/method has a specific optional parameter.
   *
   * @param mixed $parameter
   *  Either the name of the parameter (with or without leading $), or a
   *  ParameterNode.
   * @param string $type
   *  Optional type hint to check.
   *
   * @return boolean
   */
  public function hasOptionalParameter($parameter, $type = NULL) {
    return $this->hasParameter($parameter, $type) && $this->getParameterByName($parameter)->isOptional();
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
      ->children(Filter::isInstanceOf('\Pharborist\ParameterNode'))
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
      ->children(Filter::isInstanceOf('\Pharborist\ParameterNode'))
      ->filter(function(ParameterNode $parameter) {
        $value = $parameter->getValue();
        return isset($value);
      });
  }

  /**
   * Returns if the final parameter is variadic (PHP 5.6+), as in:
   * `function foobaz($a, $b, ...$c)`
   *
   * @return boolean
   */
  public function isVariadic() {
    $parameters = $this->getParameters();
    $last_parameter = $parameters[count($parameters) - 1];
    // In a variadic function, the last parameter is the only one which is
    // allowed to be variadic.
    return $last_parameter->getVariadic() !== NULL;
  }
}
