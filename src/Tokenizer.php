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

  private function parseToken($token, $filename) {
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
    $length = strlen($text);
    $newlineCount = substr_count($text, "\n");
    if ($newlineCount > 0) {
      $this->lineNo += $newlineCount;
      $this->colNo = $length - strrpos($text, "\n");
    } else {
      $this->colNo += $length;
    }
    $this->byteOffset += $length;
    return $this->createToken($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
  }

  private function createToken($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset) {
    switch ($type) {
      case T_VARIABLE:
        return new VariableNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_LNUMBER:
        return new IntegerNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_DNUMBER:
        return new FloatNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_CONSTANT_ENCAPSED_STRING:
        return new StringNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_LINE:
        return new LineMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_FILE:
        return new FileMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_DIR:
        return new DirMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_FUNC_C:
        return new FunctionMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_CLASS_C:
        return new ClassMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_TRAIT_C:
        return new TraitMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_METHOD_C:
        return new MethodMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_NS_C:
        return new NamespaceMagicConstantNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_COMMENT:
        return new CommentNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_DOC_COMMENT:
        return new DocCommentNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      case T_WHITESPACE:
        return new WhitespaceNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
      default:
        return new TokenNode($type, $text, $filename, $lineNo, $newlineCount, $colNo, $byteOffset);
    }
  }

  /**
   * @param string $source
   *   PHP source code.
   * @param string $filename
   *   Filename containing source code.
   * @return TokenNode[]
   *   Tokens.
   */
  public function getAll($source, $filename = 'source') {
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
