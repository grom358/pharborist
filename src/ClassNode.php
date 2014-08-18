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
   * @param $class_name
   * @return ClassNode
   */
  public static function create($class_name) {
    $class_node = Parser::parseSnippet("class $class_name {}")->firstChild();
    return $class_node;
  }

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
   * @param boolean $is_abstract
   * @return $this
   */
  public function setAbstract($is_abstract) {
    if ($is_abstract) {
      if (!isset($this->abstract)) {
        $this->abstract = new TokenNode(T_ABSTRACT, 'abstract');
        $this->prepend([
          $this->abstract,
          new TokenNode(T_WHITESPACE, ' '),
        ]);
        $this->setFinal(FALSE);
      }
    }
    else {
      if (isset($this->abstract)) {
        // Remove whitespace.
        $this->abstract->next()->remove();
        // Remove abstract.
        $this->abstract->remove();
      }
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
   * @param boolean $is_final
   * @return $this
   */
  public function setFinal($is_final) {
    if ($is_final) {
      if (!isset($this->final)) {
        $this->final = new TokenNode(T_FINAL, 'final');
        $this->prepend([
          $this->final,
          new TokenNode(T_WHITESPACE, ' '),
        ]);
        $this->setAbstract(FALSE);
      }
    }
    else {
      if (isset($this->final)) {
        // Remove whitespace.
        $this->final->next()->remove();
        // Remove final.
        $this->final->remove();
      }
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
   * @param string|NameNode $extends
   * @return $this
   */
  public function setExtends($extends) {
    if ($extends === NULL) {
      if (isset($this->extends)) {
        // Remove whitespace after extends keyword.
        $this->extends->previous()->remove();
        // Remove extends keyword.
        $this->extends->previous()->remove();
        // Remove whitespace before extends keyword.
        $this->extends->previous()->remove();
        // Remove extends namespace.
        $this->extends->remove();
      }
    }
    else {
      if (is_string($extends)) {
        $extends = NameNode::create($extends);
      }
      if (isset($this->extends)) {
        $this->extends->replaceWith($extends);
      }
      else {
        $this->name->after([
          new TokenNode(T_WHITESPACE, ' '),
          new TokenNode(T_EXTENDS, 'extends'),
          new TokenNode(T_WHITESPACE, ' '),
          $extends
        ]);
      }
      $this->extends = $extends;
    }
    return $this;
  }

  /**
   * @return NameNode[]
   */
  public function getImplements() {
    return $this->implements->getItems();
  }

  /**
   * @param string|NameNode|CommaListNode|array|NULL $implements
   * @throws \InvalidArgumentException
   * @return $this
   */
  public function setImplements($implements) {
    if ($implements === NULL) {
      if (isset($this->implements)) {
        // Remove whitespace after implements keyword.
        $this->implements->previous()->remove();
        // Remove implements keyword
        $this->implements->previous()->remove();
        // Remove whitespace before implements keyword.
        $this->implements->previous()->remove();
        // Remove implements list.
        $this->implements->remove();
      }
    }
    else {
      // Type conversions.
      if (is_string($implements)) {
        $implements = NameNode::create($implements);
      }
      if ($implements instanceof NameNode) {
        $implementList = new CommaListNode();
        $implementList->append($implements);
        $implements = $implementList;
      }
      if (is_array($implements)) {
        $implementList = new CommaListNode();
        foreach ($implements as $implement) {
          if (is_string($implement)) {
            $implementList->append(NameNode::create($implement));
          }
          elseif ($implement instanceof NameNode) {
            $implementList->append($implement);
          }
          else {
            throw new \InvalidArgumentException('Invalid $implements argument');
          }
        }
        $implements = $implementList;
      }
      // Set implements.
      if (isset($this->implements)) {
        $this->implements->replaceWith($implements);
      }
      else {
        $after = isset($this->extends) ? $this->extends : $this->name;
        $after->after([
          new TokenNode(T_WHITESPACE, ' '),
          new TokenNode(T_IMPLEMENTS, 'implements'),
          new TokenNode(T_WHITESPACE, ' '),
          $implements
        ]);
      }
      $this->implements = $implements;
    }
    return $this;
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

  /**
   * Add property to class.
   *
   * @param string|ClassMemberListNode $property
   * @return $this
   */
  public function appendProperty($property) {
    if (is_string($property)) {
      $property = ClassMemberListNode::create($property);
    }
    $nl = Settings::get('formatter.nl');
    $indent = Settings::get('formatter.indent');
    $properties = $this->statements->children(Filter::isInstanceOf('\Pharborist\ClassMemberListNode'));
    if ($properties->count() === 0) {
      $this->statements->prepend([
        new TokenNode(T_WHITESPACE, $nl . $indent),
        $property,
        new TokenNode(T_WHITESPACE, $nl),
      ]);
    }
    else {
      $properties->last()->after([
        new TokenNode(T_WHITESPACE, $nl . $nl . $indent),
        $property
      ]);
    }
    return $this;
  }

  /**
   * Add method to class.
   *
   * @param string|ClassMethodNode $method
   * @return $this
   */
  public function appendMethod($method) {
    if (is_string($method)) {
      $method = ClassMethodNode::create($method);
    }
    $nl = Settings::get('formatter.nl');
    $indent = Settings::get('formatter.indent');
    $this->statements->append([
      new TokenNode(T_WHITESPACE, $nl . $indent),
      $method,
      new TokenNode(T_WHITESPACE, $nl),
    ]);
    return $this;
  }
}
