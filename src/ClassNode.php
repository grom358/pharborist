<?php
namespace Pharborist;

use \SplBool as Boolean;

/**
 * Class declaration.
 */
class ClassNode extends StatementNode {
  /**
   * @var DocCommentNode
   */
  protected $docComment;

  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var TokenNode
   */
  protected $name;

  /**
   * @var NameNode
   */
  protected $extends;

  /**
   * @var CommaListNode
   */
  protected $implements;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @return DocCommentNode
   */
  public function getDocComment() {
    return $this->docComment;
  }

  /**
   * @return TokenNode
   */
  public function getAbstract() {
    return $this->abstract;
  }
  
  /**
   * @return $this
   */
  public function setAbstract(Boolean $status) {
    $this->abstract = ($status ? new TokenNode(T_ABSTRACT, 'abstract') : NULL);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }
  
  /**
   * @return $this
   */
  public function setFinal(Boolean $status) {
    $this->final = ($status ? new TokenNode(T_FINAL, 'final') : NULL);
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return NameNode
   */
  public function getExtends() {
    return $this->extends;
  }
  
  /**
   * @todo
   *  Throw an exception if $parent and $this->name have identical values.
   *
   * @return $this
   */
  public function setExtends(NameNode $parent = NULL) {
    $this->extends = $parent;
    return $this;
  }

  /**
   * @return NameNode[]
   */
  public function getImplements() {
    return $this->implements->getItems();
  }

  /**
   * @return ClassStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }
}
