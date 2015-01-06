<?php
namespace Pharborist;

class TokenTest extends \PHPUnit_Framework_TestCase {
  /**
   * Helper function to tokenize a single PHP token.
   *
   * @param string $str
   *   String contents of token.
   *
   * @return TokenNode
   */
  protected function parsePhpToken($str) {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<?php ' . $str);
    return end($tokens);
  }

  /**
   * @param TokenNode $expected
   * @param TokenNode $actual
   * @param bool $check_contents
   *   (optional) TRUE check the contents.
   */
  protected function assertToken($expected, $actual, $check_contents = TRUE) {
    $this->assertEquals($expected->getTypeName(), $actual->getTypeName());
    $this->assertInstanceOf(get_class($expected), $actual);
    if ($check_contents) {
      $this->assertEquals($expected->getText(), $actual->getText());
    }
  }

  /**
   * @param string $str
   *   String contents of token.
   * @param TokenNode $token
   *   The token returned from Token class method.
   * @param bool $check_contents
   *   (optional) TRUE check the contents.
   */
  protected function assertPhpToken($str, $token, $check_contents = TRUE) {
    $expected = $this->parsePhpToken($str);
    $actual = Token::parse($str);
    $this->assertToken($expected, $actual, $check_contents);
    $this->assertToken($expected, $token, $check_contents);
  }

  public function testPhpTokens() {
    $this->assertPhpToken('abstract', Token::_abstract());
    $this->assertPhpToken('array', Token::_array());
    $this->assertPhpToken('as', Token::_as());
    $this->assertPhpToken('break', Token::_break());
    $this->assertPhpToken('callable', Token::_callable());
    $this->assertPhpToken('case', Token::_case());
    $this->assertPhpToken('catch', Token::_catch());
    $this->assertPhpToken('class', Token::_class());
    $this->assertPhpToken('clone', Token::_clone());
    $this->assertPhpToken('const', Token::_const());
    $this->assertPhpToken('continue', Token::_continue());
    $this->assertPhpToken('declare', Token::_declare());
    $this->assertPhpToken('default', Token::_default());
    $this->assertPhpToken('die', Token::_die());
    $this->assertPhpToken('do', Token::_do());
    $this->assertPhpToken('echo', Token::_echo());
    $this->assertPhpToken('else', Token::_else());
    $this->assertPhpToken('elseif', Token::_elseIf());
    $this->assertPhpToken('empty', Token::_empty());
    $this->assertPhpToken('enddeclare', Token::_endDeclare());
    $this->assertPhpToken('endfor', Token::_endFor());
    $this->assertPhpToken('endforeach', Token::_endForeach());
    $this->assertPhpToken('endif', Token::_endIf());
    $this->assertPhpToken('endswitch', Token::_endSwitch());
    $this->assertPhpToken('endwhile', Token::_endWhile());
    $this->assertPhpToken('eval', Token::_eval());
    $this->assertPhpToken('exit', Token::_exit());
    $this->assertPhpToken('extends', Token::_extends());
    $this->assertPhpToken('final', Token::_final());
    $this->assertPhpToken('for', Token::_for());
    $this->assertPhpToken('foreach', Token::_foreach());
    $this->assertPhpToken('function', Token::_function());
    $this->assertPhpToken('global', Token::_global());
    $this->assertPhpToken('goto', Token::_goto());
    $this->assertPhpToken('if', Token::_if());
    $this->assertPhpToken('implements', Token::_implements());
    $this->assertPhpToken('include', Token::_include());
    $this->assertPhpToken('include_once', Token::_includeOnce());
    $this->assertPhpToken('instanceof', Token::_instanceOf());
    $this->assertPhpToken('insteadof', Token::_insteadOf());
    $this->assertPhpToken('interface', Token::_interface());
    $this->assertPhpToken('isset', Token::_isset());
    $this->assertPhpToken('list', Token::_list());
    $this->assertPhpToken('namespace', Token::_namespace());
    $this->assertPhpToken('new', Token::_new());
    $this->assertPhpToken('print', Token::_print());
    $this->assertPhpToken('private', Token::_private());
    $this->assertPhpToken('protected', Token::_protected());
    $this->assertPhpToken('public', Token::_public());
    $this->assertPhpToken('require', Token::_require());
    $this->assertPhpToken('require_once', Token::_requireOnce());
    $this->assertPhpToken('return', Token::_return());
    $this->assertPhpToken('static', Token::_static());
    $this->assertPhpToken('switch', Token::_switch());
    $this->assertPhpToken('throw', Token::_throw());
    $this->assertPhpToken('trait', Token::_trait());
    $this->assertPhpToken('try', Token::_try());
    $this->assertPhpToken('unset', Token::_unset());
    $this->assertPhpToken('use', Token::_use());
    $this->assertPhpToken('var', Token::_var());
    $this->assertPhpToken('while', Token::_while());
    $this->assertPhpToken('and', Token::logicalAnd());
    $this->assertPhpToken('or', Token::logicalOr());
    $this->assertPhpToken('xor', Token::logicalXor());
    $this->assertPhpToken('(array)', Token::arrayCast());
    $this->assertPhpToken('(bool)', Token::booleanCast(), FALSE);
    $this->assertPhpToken('(boolean)', Token::booleanCast());
    $this->assertPhpToken('(real)', Token::doubleCast(), FALSE);
    $this->assertPhpToken('(float)', Token::doubleCast(), FALSE);
    $this->assertPhpToken('(double)', Token::doubleCast());
    $this->assertPhpToken('(int)', Token::integerCast(), FALSE);
    $this->assertPhpToken('(integer)', Token::integerCast());
    $this->assertPhpToken('(object)', Token::objectCast());
    $this->assertPhpToken('(string)', Token::stringCast());
    $this->assertPhpToken('(unset)', Token::unsetCast());
    $this->assertPhpToken('__halt_compiler', Token::haltCompiler());
    $this->assertPhpToken('__CLASS__', Token::classConstant());
    $this->assertPhpToken('__DIR__', Token::dirConstant());
    $this->assertPhpToken('__FILE__', Token::fileConstant());
    $this->assertPhpToken('__FUNCTION__', Token::functionConstant());
    $this->assertPhpToken('__LINE__', Token::lineConstant());
    $this->assertPhpToken('__METHOD__', Token::methodConstant());
    $this->assertPhpToken('__NAMESPACE__', Token::namespaceConstant());
    $this->assertPhpToken('__TRAIT__', Token::traitConstant());
    $this->assertPhpToken('=>', Token::doubleArrow());
    $this->assertPhpToken('->', Token::objectOperator());
    $this->assertPhpToken('::', Token::doubleColon());
    $this->assertPhpToken('&=', Token::bitwiseAndAssign());
    $this->assertPhpToken('|=', Token::bitwiseOrAssign());
    $this->assertPhpToken('^=', Token::bitwiseXorAssign());
    $this->assertPhpToken('*=', Token::multiplyAssign());
    $this->assertPhpToken('/=', Token::divideAssign());
    $this->assertPhpToken('%=', Token::modulusAssign());
    $this->assertPhpToken('+=', Token::addAssign());
    $this->assertPhpToken('-=', Token::subtractAssign());
    $this->assertPhpToken('.=', Token::concatAssign());
    $this->assertPhpToken('===', Token::isIdentical());
    $this->assertPhpToken('==', Token::isEqual());
    $this->assertPhpToken('!==', Token::isNotIdentical());
    $this->assertPhpToken('!=', Token::isNotEqual());
    $this->assertPhpToken('<=', Token::isLessThanOrEqual());
    $this->assertPhpToken('>=', Token::isGreaterThanOrEqual());
    $this->assertPhpToken('&&', Token::booleanAnd());
    $this->assertPhpToken('||', Token::booleanOr());
    $this->assertPhpToken('<<', Token::bitwiseShiftLeft());
    $this->assertPhpToken('<<=', Token::bitwiseShiftLeftAssign());
    $this->assertPhpToken('>>', Token::bitwiseShiftRight());
    $this->assertPhpToken('>>=', Token::bitwiseShiftRightAssign());
    $this->assertPhpToken('--', Token::decrement());
    $this->assertPhpToken('++', Token::increment());
    $this->assertPhpToken('`', Token::backtick());
    $this->assertPhpToken('~', Token::bitwiseNot());
    $this->assertPhpToken('!', Token::not());
    $this->assertPhpToken('@', Token::suppress());
    $this->assertPhpToken('%', Token::modulus());
    $this->assertPhpToken('^', Token::bitwiseXor());
    $this->assertPhpToken('&', Token::bitwiseAnd());
    $this->assertPhpToken('*', Token::multiply());
    $this->assertPhpToken('(', Token::openParen());
    $this->assertPhpToken(')', Token::closeParen());
    $this->assertPhpToken('-', Token::subtract());
    $this->assertPhpToken('+', Token::add());
    $this->assertPhpToken('=', Token::assign());
    $this->assertPhpToken('{', Token::openBrace());
    $this->assertPhpToken('}', Token::closeBrace());
    $this->assertPhpToken('[', Token::openBracket());
    $this->assertPhpToken(']', Token::closeBracket());
    $this->assertPhpToken('\\', Token::namespaceSeparator());
    $this->assertPhpToken('|', Token::bitwiseOr());
    $this->assertPhpToken(':', Token::colon());
    $this->assertPhpToken(';', Token::semiColon());
    $this->assertPhpToken('"', Token::doubleQuote());
    $this->assertPhpToken('<', Token::isLessThan());
    $this->assertPhpToken(',', Token::comma());
    $this->assertPhpToken('>', Token::isGreaterThan());
    $this->assertPhpToken('.', Token::concat());
    $this->assertPhpToken('?', Token::ternaryOperator());
    $this->assertPhpToken('/', Token::divide());
    $this->assertPhpToken(' ', Token::space());
    $this->assertPhpToken('?>', Token::closeTag());
  }

  public function testOpenTag() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll("<?php\n");
    $this->assertToken($tokens[0], Token::openTag());
  }

  public function testOpenEchoTag() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<?=$test?>');
    $this->assertToken($tokens[0], Token::openEchoTag());
  }

  public function testCurlyOpen() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<?php "{$test}";');
    $expected = $tokens[2];
    $this->assertToken($expected, Token::curlyOpen());
  }

  public function testDollarOpenCurly() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<?php "${test}";');
    $expected = $tokens[2];
    $this->assertToken($expected, Token::dollarOpenCurly());
  }

  public function testHeredoc() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<?php <<<EOF' . PHP_EOL . 'EOF;' . PHP_EOL);
    $start_heredoc = $tokens[1];
    $end_heredoc = $tokens[2];
    $this->assertToken($start_heredoc, Token::startHeredoc('EOF'));
    $this->assertToken($end_heredoc, Token::endHeredoc('EOF'));
  }

  public function testNowdoc() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll("<?php <<<'EOF'" . PHP_EOL . 'EOF;' . PHP_EOL);
    $start_nowdoc = $tokens[1];
    $end_nowdoc = $tokens[2];
    $this->assertToken($start_nowdoc, Token::startNowdoc('EOF'));
    $this->assertToken($end_nowdoc, Token::endNowdoc('EOF'));
  }

  public function testInteger() {
    $this->assertPhpToken('42', Token::integer('42'));
    $this->assertPhpToken('07', Token::integer('07'));
    $this->assertPhpToken('0x7F', Token::integer('0x7F'));
    $this->assertPhpToken('0b101', Token::integer('0b101'));
  }

  public function testDecimal() {
    $this->assertPhpToken('42.3', Token::decimalNumber('42.3'));
    $this->assertPhpToken('0.42', Token::decimalNumber('0.42'));
    $this->assertPhpToken('.42', Token::decimalNumber('.42'));
    $this->assertPhpToken('42.0', Token::decimalNumber('42.0'));
    $this->assertPhpToken('42.', Token::decimalNumber('42.'));
    $this->assertPhpToken('42e3', Token::decimalNumber('42e3'));
    $this->assertPhpToken('42E3', Token::decimalNumber('42E3'));
    $this->assertPhpToken('42e+3', Token::decimalNumber('42e+3'));
    $this->assertPhpToken('42e-3', Token::decimalNumber('42e-3'));
    $this->assertPhpToken('0.42e3', Token::decimalNumber('0.42e3'));
    $this->assertPhpToken('.42e3', Token::decimalNumber('.42e3'));
    $this->assertPhpToken('42.0e3', Token::decimalNumber('42.0e3'));
    $this->assertPhpToken('42.e3', Token::decimalNumber('42.e3'));
  }

  public function testVariable() {
    $this->assertPhpToken('$hello', Token::variable('$hello'));
  }

  public function testInline() {
    $tokenizer = new Tokenizer();
    $tokens = $tokenizer->getAll('<html>');
    $this->assertToken($tokens[0], Token::inlineHtml('<html>'));
  }

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testInvalid() {
    $this->assertPhpToken('$42', NULL);
  }

  /**
   * @requires PHP 5.5
   */
  public function test55() {
    $this->assertPhpToken('yield', Token::_yield());
    $this->assertPhpToken('finally', Token::_finally());
  }

  /**
   * @requires PHP 5.6
   */
  public function test56() {
    $this->assertPhpToken('...', Token::splat());
  }
}
