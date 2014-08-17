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
   * @return $this
   */
  public function setAbstract($is_abstract) {
    if ($is_abstract) {
      $this->abstract = new TokenNode(T_ABSTRACT, 'abstract');
      // @todo: Add a space after abstract?
      // Abstract classes are meant to be extended, so they're never final.
      $this->setFinal(FALSE);
    }
    else {
      $this->abstract = NULL;
    }
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
  public function setFinal($is_final) {
    if ($is_final) {
      $this->final = new TokenNode(T_FINAL, 'final');
      // @todo: Add a space after final?
      // Final classes cannot be extended, so they're never abstract.
      $this->setAbstract(FALSE);
    }
    else {
      $this->final = NULL;
    }
    return $this;
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
