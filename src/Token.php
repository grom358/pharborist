<?php
namespace Pharborist;

/**
 * Factory class for tokens.
 *
 * Keywords are prefix with underscore _ since can't name function as keyword.
 */
class Token {
  public static function parse($text) {
    switch ($text) {
      case 'public':
        return static::_public();
      case 'protected':
        return static::_protected();
      case 'private':
        return static::_private();
      case 'abstract':
        return static::_abstract();
      case 'static':
        return static::_static();
      case 'final':
        return static::_final();
      case 'extends':
        return static::_extends();
      case ',':
        return static::comma();
      case ';':
        return static::semiComma();
      case ' ':
        return static::space();
      case '&':
        return static::reference();
      case '=':
        return static::assign();
      case '{':
        return static::openBrace();
      case '}':
        return static::closeBrace();
      default:
        // @todo handle all tokens as per http://php.net/manual/en/tokens.php
        throw new \InvalidArgumentException("Unable to parse '{$text}'");
    }
  }

  public static function _abstract() {
    return new TokenNode(T_ABSTRACT, 'abstract');
  }

  public static function _static() {
    return new TokenNode(T_STATIC, 'static');
  }

  public static function _final() {
    return new TokenNode(T_FINAL, 'final');
  }

  public static function _public() {
    return new TokenNode(T_PUBLIC, 'public');
  }

  public static function _protected() {
    return new TokenNode(T_PROTECTED, 'protected');
  }

  public static function _private() {
    return new TokenNode(T_PROTECTED, 'private');
  }

  public static function _extends() {
    return new TokenNode(T_EXTENDS, 'extends');
  }

  public static function _implements() {
    return new TokenNode(T_IMPLEMENTS, 'implements');
  }

  public static function space() {
    return new WhitespaceNode(T_WHITESPACE, ' ');
  }

  public static function assign() {
    return new TokenNode('=', '=');
  }

  public static function reference() {
    return new TokenNode('&', '&');
  }

  public static function comma() {
    return new TokenNode(',', ',');
  }

  public static function semiComma() {
    return new TokenNode(';', ';');
  }

  public static function openBrace() {
    return new TokenNode('{', '{');
  }

  public static function closeBrace() {
    return new TokenNode('}', '}');
  }

  public static function string($text) {
    return new TokenNode(T_STRING, $text);
  }
}
