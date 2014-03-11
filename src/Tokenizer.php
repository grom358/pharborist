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
    return new Token($type, $text, $lineNo, $colNo);
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
