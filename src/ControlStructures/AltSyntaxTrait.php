<?php
namespace Pharborist\ControlStructures;

use Pharborist\TokenNode;

/**
 * Trait for control structures that support alternative syntax.
 */
trait AltSyntaxTrait {
  /**
   * @var TokenNode
   */
  protected $openColon;

  /**
   * @var TokenNode
   */
  protected $endKeyword;

  /**
   * The colon (':') delimiter for body of statements.
   *
   * @return TokenNode
   */
  public function getOpenColon() {
    return $this->openColon;
  }

  /**
   * The end keyword delimiter for end of control structure.
   *
   * @return TokenNode
   */
  public function getEndKeyword() {
    return $this->endKeyword;
  }

  /**
   * Return if control structure is using the altnerative syntax.
   *
   * @return bool
   */
  public function isAlterativeSyntax() {
    return $this->endKeyword !== NULL;
  }
}
