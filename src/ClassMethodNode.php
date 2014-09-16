<?php
namespace Pharborist;

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
    $class_node = Parser::parseSnippet("class Property {public function {$method_name}() {}}");
    $method_node = $class_node->getBody()->firstChild()->remove();
    return $method_node;
  }

  /**
   * Create method from function declaration.
   *
   * @param FunctionDeclarationNode $function_node
   * @return ClassMethodNode
   */
  public static function fromFunction(FunctionDeclarationNode $function_node) {
    $method = static::create($function_node->getName()->getText());
    $function_node = clone $function_node;
    $method->getParameterList()->replaceWith($function_node->getParameterList());
    $body = $function_node->getBody();
    $body->addIndent(Settings::get('formatter.indent'));
    $method->getBody()->replaceWith($body);
    // Indenting a function only indents its statements, however since we have
    // converted a function into a method we also wish to indent the closing
    // brace, so get the last whitespace node and add an indent to it.
    /** @var WhitespaceNode $ws_node */
    $ws = $method->getBody()->children(Filter::isInstanceOf('\Pharborist\WhitespaceNode'));
    if ($ws->count() > 0) {
      $ws_node = $ws->last()[0];
      $text = $ws_node->getText();
      $ws_node->setText($text . Settings::get('formatter.indent'));
    }
    return $method;
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
