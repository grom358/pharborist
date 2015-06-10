<?php
namespace Pharborist\Objects;

use Pharborist\Constants\ConstantDeclarationNode;
use Pharborist\Constants\ConstantDeclarationStatementNode;
use Pharborist\Filter;
use Pharborist\FormatterFactory;
use Pharborist\Namespaces\IdentifierNameTrait;
use Pharborist\NodeCollection;
use Pharborist\Namespaces\NameNode;
use Pharborist\Parser;
use Pharborist\StatementNode;
use Pharborist\DocCommentTrait;
use Pharborist\CommaListNode;
use Pharborist\StatementBlockNode;
use Pharborist\Token;

/**
 * An interface declaration.
 */
class InterfaceNode extends StatementNode {
  use IdentifierNameTrait;
  use DocCommentTrait;

  /**
   * @var CommaListNode
   */
  protected $extends;

  /**
   * @var StatementBlockNode
   */
  protected $statements;

  /**
   * @param string $interface_name
   * @return ClassNode
   */
  public static function create($interface_name) {
    $interface_node = Parser::parseSnippet("interface $interface_name {}")->remove();
    return $interface_node;
  }

  /**
   * @return CommaListNode
   */
  public function getExtendList() {
    return $this->extends;
  }

  /**
   * @return NodeCollection|NameNode[]
   */
  public function getExtends() {
    if (!isset($this->extends)) {
      return new NodeCollection();
    }
    return $this->extends->getItems();
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
      if (!is_array($extends)) {
        $extends = [$extends];
      }
      $extendsList = new CommaListNode();
      foreach ($extends as $extend) {
        if (is_string($extend)) {
          $extendsList->appendItem(NameNode::create($extend));
        }
        elseif ($extend instanceof NameNode) {
          $extendsList->appendItem($extend);
        }
        else {
          throw new \InvalidArgumentException('Invalid $extends argument');
        }
      }
      $extends = $extendsList;
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
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->statements;
  }

  /**
   * @return NodeCollection|InterfaceStatementNode[]
   */
  public function getStatements() {
    return $this->statements->getStatements();
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

  /**
   * @return NodeCollection|InterfaceMethodNode[]
   */
  public function getMethods() {
    return $this->statements->children(Filter::isInstanceOf('\Pharborist\Objects\InterfaceMethodNode'));
  }

  /**
   * Returns a method by name, if it exists.
   *
   * @param string $name
   *  The method name.
   *
   * @return InterfaceMethodNode|NULL
   */
  public function getMethod($name) {
    $methods = $this
      ->getMethods()
      ->filter(function (InterfaceMethodNode $method) use ($name) {
        return $method->getName()->getText() === $name;
      });
    return $methods->isEmpty() ? NULL : $methods[0];
  }

  /**
   * Returns if the interface has the named method.
   *
   * @param string $name
   *  The method name.
   *
   * @return boolean
   */
  public function hasMethod($name) {
    return in_array($name, $this->getMethodNames());
  }

  /**
   * Returns the names of all interface methods.
   *
   * @return string[]
   */
  public function getMethodNames() {
    return array_map(function (InterfaceMethodNode $node) {
      return $node->getName()->getText();
    }, $this->getMethods()->toArray());
  }


  /**
   * Adds a method to interface.
   *
   * @param InterfaceMethodNode|string $method
   *   The method to append. Can either be an existing method, or a string (a
   *   new public method will be created with that name).
   *
   * @return $this
   */
  public function appendMethod($method) {
    if (is_string($method)) {
      $method = InterfaceMethodNode::create($method);
    }
    $this->statements->lastChild()->before($method);
    FormatterFactory::format($this);
    return $this;
  }
}
