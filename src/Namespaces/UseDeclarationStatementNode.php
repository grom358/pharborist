<?php
namespace Pharborist\Namespaces;

use Pharborist\CommaListNode;
use Pharborist\NodeCollection;
use Pharborist\StatementNode;
use Pharborist\TokenNode;

/**
 * Use declaration statement, importing several classes, functions, or constants
 * into a namespace.
 *
 * Example:
 * ```
 * use Jones, Gilliam, Cleese as Idle;
 * ```
 */
class UseDeclarationStatementNode extends StatementNode {
  /**
   * The function keyword.
   *
   * This property only has value if the use declaration statement is for
   * importing a function. Eg. use function MyNamespace\my_func;
   *
   * @var TokenNode
   */
  protected $useFunction;

  /**
   * The const keyword.
   *
   * This property only has value if the use declaration statement is for
   * importing a const. Eg. use const MyNamespace\MY_CONST;
   *
   * @var TokenNode
   */
  protected $useConst;

  /**
   * Test whether use declaration imports a class.
   *
   * @param string|NULL $class_name
   *   (Optional) Class name to check if being imported by use statement.
   *
   * @return bool
   *   TRUE if this use declaration imports class.
   */
  public function importsClass($class_name = NULL) {
    if ($this->useFunction || $this->useConst) {
      return FALSE;
    }
    if ($class_name) {
      foreach ($this->getDeclarations() as $declaration) {
        if ($declaration->getName()->getPath() === $class_name) {
          return TRUE;
        }
      }
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Test whether use declaration imports a function.
   *
   * @param string|NULL $function_name
   *   (Optional) Function name to check if being imported by use statement.
   *
   * @return bool
   *   TRUE if this use declaration imports function.
   */
  public function importsFunction($function_name = NULL) {
    if (!$this->useFunction) {
      return FALSE;
    }
    if ($function_name) {
      foreach ($this->getDeclarations() as $declaration) {
        if ($declaration->getName()->getPath() === $function_name) {
          return TRUE;
        }
      }
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Test whether use declaration imports a constant.
   *
   * @param string|NULL $const_name
   *   (Optional) Constant name to check if being imported by use statement.
   *
   * @return bool
   *   TRUE if this use declaration imports constant.
   */
  public function importsConst($const_name = NULL) {
    if (!$this->useConst) {
      return FALSE;
    }
    if ($const_name) {
      foreach ($this->getDeclarations() as $declaration) {
        if ($declaration->getName()->getPath() === $const_name) {
          return TRUE;
        }
      }
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * @var CommaListNode
   */
  protected $declarations;

  /**
   * @return CommaListNode
   */
  public function getDeclarationList() {
    return $this->declarations;
  }

  /**
   * @return NodeCollection|UseDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->declarations->getItems();
  }
}
