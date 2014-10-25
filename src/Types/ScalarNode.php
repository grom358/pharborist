<?php
namespace Pharborist\Types;

use Pharborist\NodeInterface;

/**
 * A single, static value that is not composed of other values (like an array). The
 * following are all valid ScalarNodes:
 *
 * - Non-interpolated strings: `'Herro'`, `"Why the long face?"`
 * - Integers: 0, 30, -1
 * - Floats: 3.141, 8.75
 * - Booleans: `TRUE`, `FALSE`
 * - Null
 */
interface ScalarNode extends NodeInterface {
  /**
   * @return mixed
   */
  public function toValue();
}
