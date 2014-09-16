<?php
namespace Pharborist;

/**
 * A single value that is an atomic unit, and not composed of other values.
 */
interface ScalarNode extends NodeInterface {
  /**
   * @return mixed
   */
  public function getValue();
}
