<?php
namespace Pharborist;

/**
 * Interface for any node which can determine its own name relative to a
 * namespace.
 */
interface NameResolutionInterface {

  /**
   * Resolves and returns the fully qualified name of the node. The fully
   * qualified name begins with \ and interprets the optional 'namespace'
   * token at the beginning of the name, if it's there.
   *
   * @return string
   */
  public function getFullyQualifiedName();

  /**
   * Returns the partially qualified name of the node, exactly as written in
   * the parsed code. If the name begins with the 'namespace' token, it is NOT
   * interpreted.
   *
   * @return string
   */
  public function getQualifiedName();

  /**
   * Returns the unqualified name of the node. If the name is qualified at all,
   * this should return the final part of the name (everything after the final
   * backslash).
   *
   * @return string
   */
  public function getUnqualifiedName();

  /**
   * Returns the partially qualified name of the node, relative to the current
   * namespace. Basically just the fully qualified name with the closest
   * namespace's fully qualified name trimmed off.
   *
   * @return string
   */
  public function getQualifiedRelativeName();

}
