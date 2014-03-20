<?php
namespace Pharborist;

/**
 * A namespace path to function, constant, class, trait or interface.
 * Eg. MyNamespace\MyClass
 * @package Pharborist
 */
class NamespacePathNode extends Node {
  /**
   * Get the namespace path.
   * @return string
   */
  public function getPath() {
    $path = '';
    /** @var TokenNode $child */
    foreach ($this->children as $child) {
      $type = $child->token->type;
      if ($type === T_NAMESPACE || $type === T_NS_SEPARATOR || $type === T_STRING) {
        $path .= $child->token->text;
      }
    }
    return $path;
  }
}
