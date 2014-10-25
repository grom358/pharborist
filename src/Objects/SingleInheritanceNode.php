<?php
namespace Pharborist\Objects;

use Pharborist\CommaListNode;
use Pharborist\DocCommentTrait;
use Pharborist\Filter;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Namespaces\NameNode;
use Pharborist\Settings;
use Pharborist\StatementBlockNode;
use Pharborist\StatementNode;
use Pharborist\Token;
use Pharborist\TokenNode;
use Pharborist\WhitespaceNode;

/**
 * Base class for ClassNode and TraitNode.
 *
 * @see ClassNode
 * @see TraitNode
 */
abstract class SingleInheritanceNode extends StatementNode {
  use DocCommentTrait;

  /**
   * @var \Pharborist\Namespaces\NameNode
   */
  protected $name;

  /**
   * @var \Pharborist\Namespaces\NameNode
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
   * @return \Pharborist\Namespaces\NameNode
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
        WhitespaceNode::create($nl . $indent),
        $property,
        WhitespaceNode::create($nl),
      ]);
    }
    else {
      $properties->last()->after([
        WhitespaceNode::create($nl . $nl . $indent),
        $property
      ]);
    }
    return $this;
  }

  /**
   * Adds a method to a class.
   *
   * @param \Pharborist\Functions\FunctionDeclarationNode|\Pharborist\Objects\ClassMethodNode|string $method
   *  The method to append. Can either be an existing method, a function (which
   *  will be converted to a public method), or a string (a new public method
   *  will be created with that name).
   *
   * @return $this
   */
  public function appendMethod($method) {
    if ($method instanceof FunctionDeclarationNode) {
      $method = ClassMethodNode::fromFunction($method);
    }
    elseif (is_string($method)) {
      $method = ClassMethodNode::create($method);
    }
    $nl = Settings::get('formatter.nl');
    $indent = Settings::get('formatter.indent');
    $this->statements->append([
      WhitespaceNode::create($nl . $indent),
      $method,
      WhitespaceNode::create($nl),
    ]);
    return $this;
  }
}
