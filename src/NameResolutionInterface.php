<?php
namespace Pharborist;

/**
 * Interface for any node which can determine its own name relative to a
 * namespace.
 */
interface NameResolutionInterface {

  /**
   * @return string
   */
  public function getFullyQualifiedName();

  /**
   * @return string
   */
  public function getQualifiedName();

  /**
   * @return string
   */
  public function getUnqualifiedName();

}
