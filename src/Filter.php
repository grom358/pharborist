<?php
namespace Pharborist;

/**
 * Factory for creating common callback filters.
 */
class Filter {
  /**
   * @param string $class_name
   * @return callable
   */
  public static function isInstanceOf($class_name) {
    return function ($node) use ($class_name) {
      return $node instanceof $class_name;
    };
  }
}
