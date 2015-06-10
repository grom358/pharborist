<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\Constants\ConstantDeclarationNode;
use Pharborist\Constants\ConstantDeclarationStatementNode;
use Pharborist\Filter;
use Pharborist\Namespaces\NameNode;
use Pharborist\NodeCollection;
use Pharborist\Parser;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * Class declaration.
 */
class ClassNode extends SingleInheritanceNode {
  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var \Pharborist\Namespaces\NameNode
   */
  protected $extends;

  /**
   * @var CommaListNode
   */
  protected $implements;

  /**
   * @param string $class_name
   * @return ClassNode
   */
  public static function create($class_name) {
    $class_node = Parser::parseSnippet("class $class_name {}")->remove();
    return $class_node;
  }

  /**
   * @return bool
   */
  public function isAbstract() {
    return $this->abstract !== NULL;
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
        $this->abstract = Token::_abstract();
        $this->prepend([
          $this->abstract,
          Token::space(),
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
   * @return bool
   */
  public function isFinal() {
    return $this->final !== NULL;
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
        $this->final = Token::_final();
        $this->prepend([
          $this->final,
          Token::space(),
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
  public function getExtends() {
    return $this->extends;
  }

  /**
   * @param string|\Pharborist\Namespaces\NameNode $extends
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
        $this->extends = NULL;
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
          Token::space(),
          Token::_extends(),
          Token::space(),
          $extends
        ]);
      }
      $this->extends = $extends;
    }
    return $this;
  }

  /**
   * @return CommaListNode
   */
  public function getImplementList() {
    return $this->implements;
  }

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getImplements() {
    if (!isset($this->implements)) {
      return new NodeCollection();
    }
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
        $this->implements = NULL;
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
            $implementList->appendItem(NameNode::create($implement));
          }
          elseif ($implement instanceof NameNode) {
            $implementList->appendItem($implement);
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
          Token::space(),
          Token::_implements(),
          Token::space(),
          $implements
        ]);
      }
      $this->implements = $implements;
    }
    return $this;
  }

  /**
   * @return NodeCollection|ConstantDeclarationNode[]
   */
  public function getConstants() {
    $constants = [];
    /** @var ConstantDeclarationStatementNode $node */
    foreach ($this->statements->children(Filter::isInstanceOf('\Pharborist\Constants\ConstantDeclarationStatementNode')) as $node) {
      $constants = array_merge($constants, $node->getDeclarations()->toArray());
    }
    return new NodeCollection($constants, FALSE);
  }
}
