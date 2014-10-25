<?php
namespace Pharborist;

use Pharborist\Constants\ClassMagicConstantNode;
use Pharborist\Constants\DirMagicConstantNode;
use Pharborist\Constants\FileMagicConstantNode;
use Pharborist\Constants\FunctionMagicConstantNode;
use Pharborist\Constants\LineMagicConstantNode;
use Pharborist\Constants\MethodMagicConstantNode;
use Pharborist\Constants\NamespaceMagicConstantNode;
use Pharborist\Constants\TraitMagicConstantNode;
use Pharborist\Types\FloatNode;
use Pharborist\Types\IntegerNode;
use Pharborist\Types\StringNode;
use Pharborist\Variables\VariableNode;

/**
 * Convert PHP source into an array of tokens.
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
    $newline_count = substr_count($text, "\n");
    if ($newline_count > 0) {
      $this->lineNo += $newline_count;
      $lines = explode("\n", $text);
      $last_line = end($lines);
      $this->colNo = strlen($last_line) + 1;
    } else {
      $this->colNo += strlen($text);
    }
    return $this->createToken($type, $text, new SourcePosition($lineNo, $colNo), $newline_count);
  }

  private function createToken($type, $text, $position, $newline_count) {
    switch ($type) {
      case T_VARIABLE:
        return new VariableNode($type, $text, $position);
      case T_LNUMBER:
        return new IntegerNode($type, $text, $position);
      case T_DNUMBER:
        return new FloatNode($type, $text, $position);
      case T_CONSTANT_ENCAPSED_STRING:
        return new StringNode($type, $text, $position);
      case T_LINE:
        return new LineMagicConstantNode($type, $text, $position);
      case T_FILE:
        return new FileMagicConstantNode($type, $text, $position);
      case T_DIR:
        return new DirMagicConstantNode($type, $text, $position);
      case T_FUNC_C:
        return new FunctionMagicConstantNode($type, $text, $position);
      case T_CLASS_C:
        return new ClassMagicConstantNode($type, $text, $position);
      case T_TRAIT_C:
        return new TraitMagicConstantNode($type, $text, $position);
      case T_METHOD_C:
        return new MethodMagicConstantNode($type, $text, $position);
      case T_NS_C:
        return new NamespaceMagicConstantNode($type, $text, $position);
      case T_COMMENT:
        return new CommentNode($type, $text, $position);
      case T_DOC_COMMENT:
        return new DocCommentNode($type, $text, $position);
      case T_WHITESPACE:
        return new WhitespaceNode($type, $text, $position, $newline_count);
      default:
        return new TokenNode($type, $text, $position);
    }
  }

  public function getAll($source) {
    $this->colNo = 1;
    $this->lineNo = 1;
    $tokens = [];
    foreach (token_get_all($source) as $rawToken) {
      $tokens[] = $this->parseToken($rawToken);
    }
    return $tokens;
  }
}
