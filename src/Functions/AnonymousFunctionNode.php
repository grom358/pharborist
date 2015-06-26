<?php
namespace Pharborist\Functions;

use Pharborist\CommaListNode;
use Pharborist\ExpressionNode;
use Pharborist\Filter;
use Pharborist\NodeCollection;
use Pharborist\ParentNode;
use Pharborist\ParenTrait;
use Pharborist\StatementBlockNode;
use Pharborist\Token;
use Pharborist\TokenNode;

/**
 * An anonymous function (closure).
 */
class AnonymousFunctionNode extends ParentNode implements ExpressionNode {
  use ParenTrait;
  use ParameterTrait;

  /**
   * @var TokenNode
   */
  protected $reference;

  /**
   * @var TokenNode
   */
  protected $lexicalUse;

  /**
   * @var TokenNode
   */
  protected $lexicalOpenParen;

  /**
   * @var TokenNode
   */
  protected $lexicalCloseParen;

  /**
   * @var CommaListNode
   */
  protected $lexicalVariables;

  /**
   * @var StatementBlockNode
   */
  protected $body;

  /**
   * @return TokenNode
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * @param boolean $is_reference
   * @return $this
   */
  public function setReference($is_reference) {
    if ($is_reference) {
      if (!isset($this->reference)) {
        $this->reference = Token::reference();
        $this->openParen->before($this->reference);
      }
    }
    else {
      if (isset($this->reference)) {
        $this->reference->remove();
      }
    }
    return $this;
  }

  /**
   * @return CommaListNode
   */
  public function getLexicalVariableList() {
    return $this->lexicalVariables;
  }

  /**
   * @return NodeCollection|LexicalVariableNode[]
   */
  public function getLexicalVariables() {
    return $this->lexicalVariables->getItems();
  }

  /**
   * @return bool
   */
  public function hasLexicalVariables() {
    return isset($this->lexicalVariables);
  }

  /**
   * @return TokenNode
   */
  public function getLexicalUse() {
    return $this->lexicalUse;
  }

  /**
   * @return TokenNode
   */
  public function getLexicalOpenParen() {
    return $this->lexicalOpenParen;
  }

  /**
   * @return TokenNode
   */
  public function getLexicalCloseParen() {
    return $this->lexicalCloseParen;
  }

  /**
   * Creates an empty lexical variables list if it does not already exist.
   */
  protected function createLexicalVariables() {
    if (!$this->hasLexicalVariables()) {
      $this->lexicalUse = Token::_use();
      $this->lexicalOpenParen = Token::openParen();
      $this->lexicalVariables = new CommaListNode();
      $this->lexicalCloseParen = Token::closeParen();
      $this->closeParen->after([
        Token::space(),
        $this->lexicalUse,
        Token::space(),
        $this->lexicalOpenParen,
        $this->lexicalVariables,
        $this->lexicalCloseParen,
      ]);
    }
  }

  /**
   * @param LexicalVariableNode $variable
   */
  public function appendLexicalVariable(LexicalVariableNode $variable) {
    $this->createLexicalVariables();
    $this->lexicalVariables->appendItem($variable);
  }

  /**
   * @param LexicalVariableNode $variable
   */
  public function prependLexicalVariable(LexicalVariableNode $variable) {
    $this->createLexicalVariables();
    $this->lexicalVariables->prependItem($variable);
  }

  /**
   * Insert argument before argument at index.
   *
   * @param LexicalVariableNode $variable
   *   The lexical variable to insert.
   * @param int $index
   *   Position to insert argument at.
   * @throws \OutOfBoundsException
   *   Index out of bounds.
   *
   * @return $this
   */
  public function insertLexicalVariable(LexicalVariableNode $variable, $index) {
    if ($index < 0) {
      throw new \OutOfBoundsException('index out of bounds');
    }
    if (!$this->hasLexicalVariables() && $index !== 0) {
      throw new \OutOfBoundsException('index out of bounds');
    }
    $this->createLexicalVariables();
    $this->lexicalVariables->insertItem($variable, $index);
  }

  public function clearLexicalVariables() {
    if ($this->hasLexicalVariables()) {
      $this->lexicalUse->nextUntil(Filter::is($this->body))->remove();
      $this->lexicalUse->remove();
    }
  }

  /**
   * @return StatementBlockNode
   */
  public function getBody() {
    return $this->body;
  }
}
