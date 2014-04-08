<?php
namespace Pharborist;

/**
 * A namespace path to function, constant, class, trait or interface.
 *
 * For example, MyNamespace\MyClass
 */
class NamespacePathNode extends ParentNode {
  /**
   * Get the namespace path.
   * @return string
   */
  public function getPath() {
    $path = '';
    /** @var TokenNode $child */
    $child = $this->head;
    while ($child) {
      $type = $child->getType();
      if ($type === T_NAMESPACE || $type === T_NS_SEPARATOR || $type === T_STRING) {
        $path .= $child->getText();
      }
      $child = $child->next;
    }
    return $path;
  }
}
