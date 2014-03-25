<?php
namespace Pharborist;

/**
 * Convert PHP source into an array of tokens.
 * @package Pharborist
 */
class Tokenizer {
  private $lineNo;
  private $colNo;

  private function parseToken($token) {
    if (is_array($token)) {
      $type = $token[0];
      $text = $token[1];
    } else {
      $type = $token;
      $text = $token;
    }
    $lineNo = $this->lineNo;
    $colNo = $this->colNo;
    $line_count = substr_count($text, "\n");
    if ($line_count > 0) {
      $this->lineNo += $line_count;
      $lines = explode("\n", $text);
      $last_line = end($lines);
      $this->colNo = strlen($last_line) + 1;
    } else {
      $this->colNo += strlen($text);
    }
    return $this->createToken($type, $text, $lineNo, $colNo);
  }

  private function createToken($type, $text, $lineNo, $colNo) {
    switch ($type) {
      case T_VARIABLE:
        return new VariableNode($type, $text, $lineNo, $colNo);
      case T_LNUMBER:
        return new IntegerNode($type, $text, $lineNo, $colNo);
      case T_DNUMBER:
        return new FloatNode($type, $text, $lineNo, $colNo);
      case T_LINE:
        return new LineMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_FILE:
        return new FileMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_DIR:
        return new DirMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_FUNC_C:
        return new FunctionMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_CLASS_C:
        return new ClassMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_TRAIT_C:
        return new TraitMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_METHOD_C:
        return new MethodMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_NS_C:
        return new NamespaceMagicConstantNode($type, $text, $lineNo, $colNo);
      case T_COMMENT:
        return new CommentNode($type, $text, $lineNo, $colNo);
      case T_DOC_COMMENT:
        return new DocCommentNode($type, $text, $lineNo, $colNo);
      case T_WHITESPACE:
        return new WhitespaceNode($type, $text, $lineNo, $colNo);
      default:
        return new TokenNode($type, $text, $lineNo, $colNo);
    }
  }

  public function getAll($source) {
    $this->colNo = 1;
    $this->lineNo = 1;
    $tokens = array();
    foreach (token_get_all($source) as $rawToken) {
      $tokens[] = $this->parseToken($rawToken);
    }
    return $tokens;
  }
}
