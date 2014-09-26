<?php
namespace Pharborist;

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
   * @return UseDeclarationNode[]
   */
  public function getDeclarations() {
    return $this->declarations->getItems();
  }
}
