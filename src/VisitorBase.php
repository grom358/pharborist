<?php
namespace Pharborist;

/**
 * Visitor
 */
class VisitorBase implements VisitorInterface {
  protected $methodCache = [];

  public static function getShortClassName($class_name) {
    $offset = strrpos($class_name, '\\');
    $offset = $offset ? $offset + 1: 0;
    return substr($class_name, $offset);
  }

  protected function getMethods(Node $node, $prefix) {
    $node_class_name = get_class($node);
    if (isset($this->methodCache[$node_class_name][$prefix])) {
      return $this->methodCache[$node_class_name][$prefix];
    }
    else {
      $methods = [];
      $classes = array_merge([$node_class_name], class_parents($node), class_implements($node));
      foreach ($classes as $class_name) {
        $class_name = static::getShortClassName($class_name);
        $method_name = $prefix . $class_name;
        if (method_exists($this, $method_name)) {
          $methods[] = $method_name;
        }
      }
      $methods = array_reverse($methods);
      $this->methodCache[$node_class_name][$prefix] = $methods;
      return $methods;
    }
  }

  public function visit(Node $node) {
    foreach ($this->getMethods($node, 'visit') as $method_name) {
      $this->$method_name($node);
    }
  }

  public function visitEnd(ParentNode $node) {
    foreach ($this->getMethods($node, 'end') as $method_name) {
      $this->$method_name($node);
    }
  }
}
