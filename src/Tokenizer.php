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
  private $byteOffset;

  private function parseToken($token, $filename = NULL) {
    if (is_array($token)) {
      $type = $token[0];
      $text = $token[1];
    } else {
      $type = $token;
      $text = $token;
    }
    $lineNo = $this->lineNo;
    $colNo = $this->colNo;
    $byteOffset = $this->byteOffset;
    $newline_count = substr_count($text, "\n");
    if ($newline_count > 0) {
      $this->lineNo += $newline_count;
      $lines = explode("\n", $text);
      $last_line = end($lines);
      $this->colNo = strlen($last_line) + 1;
    } else {
      $this->colNo += strlen($text);
    }
    $this->byteOffset += strlen($text);
    return $this->createToken($type, $text, new SourcePosition($filename, $lineNo, $colNo, $byteOffset));
  }

  private function createToken($type, $text, $position) {
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
        return new WhitespaceNode($type, $text, $position);
      default:
        return new TokenNode($type, $text, $position);
    }
  }

  /**
   * @param string $source
   *   PHP source code.
   * @param $filename
   *   (Optional) PHP filename.
   * @return TokenNode[]
   *   Tokens.
   */
  public function getAll($source, $filename = NULL) {
    $this->byteOffset = 0;
    $this->colNo = 1;
    $this->lineNo = 1;
    $tokens = [];
    foreach (token_get_all($source) as $rawToken) {
      $tokens[] = $this->parseToken($rawToken, $filename);
    }
    return $tokens;
  }
}
