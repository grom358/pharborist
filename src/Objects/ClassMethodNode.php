<?php
namespace Pharborist\Objects;

use Pharborist\Filter;
use Pharborist\Functions\FunctionDeclarationNode;
use Pharborist\Node;
use Pharborist\Parser;
use Pharborist\Settings;
use Pharborist\StatementBlockNode;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * A class method.
 */
class ClassMethodNode extends ClassStatementNode {
  use MethodTrait;

  /**
   * @var TokenNode
   */
  protected $abstract;

  /**
   * @var TokenNode
   */
  protected $final;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @param string $method_name
   * @return ClassMethodNode
   */
  public static function create($method_name) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Method {public function {$method_name}() {}}");
    $method_node = $class_node->getStatements()[0]->remove();
    return $method_node;
  }

  /**
   * Create method from function declaration.
   *
   * @param FunctionDeclarationNode $function_node
   * @return ClassMethodNode
   */
  public static function fromFunction(FunctionDeclarationNode $function_node) {
    $method_name = $function_node->getName()->getText();
    $parameters = $function_node->getParameterList()->getText();
    $indent = Settings::get('formatter.indent');
    $nl = Settings::get('formatter.nl');
    $body = str_replace($nl, $nl . $indent, $function_node->getBody()->getText());
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Method {public function {$method_name}($parameters) $body}");
    $method_node = $class_node->getStatements()[0]->remove();
    return $method_node;
  }

  /**
   * Returns the `abstract` keyword from the method declaration.
   *
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
        // Remove method body since abstract method doesn't have one.
        $this->getBody()->previous(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->remove();
        $this->getBody()->replaceWith(Token::semiColon());
        $this->body = NULL;
      }
    }
    else {
      if (isset($this->abstract)) {
        // Remove whitespace.
        $this->abstract->next()->remove();
        // Remove abstract.
        $this->abstract->remove();
        // Add empty body.
        $body = new StatementBlockNode();
        $body->append([
          Token::openBrace(),
          Token::closeBrace(),
        ]);
        $this->lastChild()->replaceWith($body);
        $this->lastChild()->before(Token::space());
        $this->body = $body;
      }
    }
    return $this;
  }

  /**
   * Returns the `final` keyword from the method declaration.
   *
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
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * Gets the fully qualified name of the method, e.g. \My\Namespaced\Class::foo.
   *
   * @return string
   */
  public function getFullyQualifiedName() {
    return $this->closest(Filter::isInstanceOf('\Pharborist\Objects\ClassNode'))->getName()->getAbsolutePath() . '::' . $this->getName();
  }

  protected function childInserted(Node $node) {
    static $visibilityTypes = [T_PUBLIC, T_PROTECTED, T_PRIVATE];
    if ($node instanceof TokenNode) {
      if ($node->getType() === '&') {
        $this->reference = $node;
      }
      elseif (in_array($node->getType(), $visibilityTypes)) {
        $this->visibility = $node;
      }
      elseif ($node->getType() === T_STATIC) {
        $this->static = $node;
      }
      elseif ($node->getType() === T_ABSTRACT) {
        $this->abstract = $node;
      }
      elseif ($node->getType() === T_FINAL) {
        $this->final = $node;
      }
    }
  }
}
