<?php
namespace Pharborist;

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
   * @var NameNode
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
   * @return TokenNode
   */
  public function getFinal() {
    return $this->final;
  }

  /**
   * @return NameNode
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the name of the declared class.
   *
   * @param string $name
   *   New name of class.
   * @return $this
   */
  public function setName($name) {
    /** @var TokenNode $class_name */
    $class_name = $this->name->firstChild();
    $class_name->setText($name);
    return $this;
  }

  /**
   * @return NameNode
   */
  public function getExtends() {
    return $this->extends;
  }

  /**
   * @return NameNode[]
   */
  public function getImplements() {
    return $this->implements->getItems();
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->statements;
  }

  /**
   * @return ClassStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
  }
}
