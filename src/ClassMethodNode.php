<?php
namespace Pharborist;

/**
 * A class method.
 */
class ClassMethodNode extends ClassStatementNode {
  use FunctionTrait;

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
  protected $static;

  /**
   * @var TokenNode
   */
  protected $visibility;

  /**
   * @param string $method_name
   * @return ClassMethodNode
   */
  public static function create($method_name) {
    /** @var ClassNode $class_node */
    $class_node = Parser::parseSnippet("class Property {public function {$method_name}() {}}")->firstChild();
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
        $this->abstract = new TokenNode(T_ABSTRACT, 'abstract');
        $this->prepend([
          $this->abstract,
          new TokenNode(T_WHITESPACE, ' '),
        ]);
        $this->setFinal(FALSE);
        // Remove method body since abstract method doesn't have one.
        $this->getBody()->previous(Filter::isInstanceOf('\Pharborist\WhitespaceNode'))->remove();
        $this->getBody()->replaceWith(new TokenNode(';', ';'));
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
          new TokenNode('{', '{'),
          new TokenNode('}', '}'),
        ]);
        $this->lastChild()->replaceWith($body);
        $this->lastChild()->before(new TokenNode(T_WHITESPACE, ' '));
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
   * @return TokenNode
   */
  public function getStatic() {
    return $this->static;
  }

  /**
   * @param boolean $is_static
   * @return $this
   */
  public function setStatic($is_static) {
    if ($is_static) {
      if (!isset($this->static)) {
        // Insert before T_FUNCTION.
        $function_token = $this->name->previous()->previous();
        $function_token->before([
          new TokenNode(T_STATIC, 'static'),
          new TokenNode(T_WHITESPACE, ' '),
        ]);
      }
    }
    else {
      if (isset($this->static)) {
        // Remove whitespace after static keyword.
        $this->static->next()->remove();
        // Remove static keyword.
        $this->static->remove();
      }
    }
    return $this;
  }

  /**
   * @return TokenNode
   */
  public function getVisibility() {
    return $this->visibility;
  }

  /**
   * @param TokenNode $visibility
   * @return $this
   */
  public function setVisibility($visibility) {
    if ($visibility === NULL) {
      // Remove whitespace after visibility keyword.
      $this->visibility->next()->remove();
      // Remove visibility keyword.
      $this->visibility->remove();
    }
    else {
      if (isset($this->visibility)) {
        $this->visibility->replaceWith($visibility);
      }
      else {
        $this->prepend([
          $visibility,
          new TokenNode(T_WHITESPACE, ' '),
        ]);
      }
    }
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
