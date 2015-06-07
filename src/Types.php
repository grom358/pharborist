<?php
namespace Pharborist;

/**
 * Helper class for phpDoc types.
 *
 * @see http://www.phpdoc.org/docs/latest/guides/types.html
 * @see https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md#appendix-a-types
 */
class Types {
  /**
   * Normalize phpDoc type keywords as per PSR-5.
   *
   * Converts:
   *  - boolean to bool
   *  - integer to int
   *  - double to float
   *  - callback to callable
   *  - scalar shorthand for bool|int|float|string
   *
   * @param string[] $types
   *   Types as returned by DocBlock library.
   *
   * @return string[]
   *   Normalize types.
   */
  public static function normalize($types) {
    $normalized_types = [];
    foreach ($types as $type) {
      switch ($type) {
        case 'boolean':
          $normalized_types[] = 'bool';
          break;
        case 'integer':
          $normalized_types[] = 'int';
          break;
        case 'double':
          $normalized_types[] = 'float';
          break;
        case 'callback':
          $normalized_types[] = 'callable';
          break;
        case 'scalar':
          $normalized_types[] = 'bool';
          $normalized_types[] = 'int';
          $normalized_types[] = 'float';
          $normalized_types[] = 'string';
          break;
        default:
          $normalized_types[] = $type;
          break;
      }
    }
    return $normalized_types;
  }
}
