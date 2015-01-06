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
use Pharborist\Variables\VariableNode;

/**
 * Factory class for tokens.
 *
 * Keywords are prefix with underscore _ since can't name function as keyword.
 */
class Token {
  /**
   * Parse a single token.
   *
   * @param $text
   *   String contents of a single token.
   *
   * @return TokenNode
   *   The parsed token.
   */
  public static function parse($text) {
    static $int_regex = <<<'EOF'
/^[+-]?(?:
  0
  | [1-9][0-9]*
  | 0[xX][0-9a-fA-F]+
  | 0[0-7]+
  | 0b[01]+
)$/x
EOF;
    static $decimal_regex = <<<EOF
/^[+-]?(?:
  [0-9]*\.[0-9]+ (?:[eE][+-]?[0-9]+)?
  | [0-9]+\.[0-9]* (?:[eE][+-]?[0-9]+)?
  | [0-9]+[eE][+-]?[0-9]+
)$/x
EOF;
    static $var_regex = '/^\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';
    switch ($text) {
      case 'abstract':
        return static::_abstract();
      case 'array':
        return static::_array();
      case 'as':
        return static::_as();
      case 'break':
        return static::_break();
      case 'callable':
        return static::_callable();
      case 'case':
        return static::_case();
      case 'catch':
        return static::_catch();
      case 'class':
        return static::_class();
      case 'clone':
        return static::_clone();
      case 'const':
        return static::_const();
      case 'continue':
        return static::_continue();
      case 'declare':
        return static::_declare();
      case 'default':
        return static::_default();
      case 'die':
        return static::_die();
      case 'do':
        return static::_do();
      case 'echo':
        return static::_echo();
      case 'else':
        return static::_else();
      case 'elseif':
        return static::_elseIf();
      case 'empty':
        return static::_empty();
      case 'enddeclare':
        return static::_endDeclare();
      case 'endfor':
        return static::_endFor();
      case 'endforeach':
        return static::_endForeach();
      case 'endif':
        return static::_endIf();
      case 'endswitch':
        return static::_endSwitch();
      case 'endwhile':
        return static::_endWhile();
      case 'eval':
        return static::_eval();
      case 'exit':
        return static::_exit();
      case 'extends':
        return static::_extends();
      case 'final':
        return static::_final();
      case 'finally':
        return static::_finally();
      case 'for':
        return static::_for();
      case 'foreach':
        return static::_foreach();
      case 'function':
        return static::_function();
      case 'global':
        return static::_global();
      case 'goto':
        return static::_goto();
      case 'if':
        return static::_if();
      case 'implements':
        return static::_implements();
      case 'include':
        return static::_include();
      case 'include_once':
        return static::_includeOnce();
      case 'instanceof':
        return static::_instanceOf();
      case 'insteadof':
        return static::_insteadOf();
      case 'interface':
        return static::_interface();
      case 'isset':
        return static::_isset();
      case 'list':
        return static::_list();
      case 'namespace':
        return static::_namespace();
      case 'new':
        return static::_new();
      case 'print':
        return static::_print();
      case 'private':
        return static::_private();
      case 'protected':
        return static::_protected();
      case 'public':
        return static::_public();
      case 'require':
        return static::_require();
      case 'require_once':
        return static::_requireOnce();
      case 'return':
        return static::_return();
      case 'static':
        return static::_static();
      case 'switch':
        return static::_switch();
      case 'throw':
        return static::_throw();
      case 'trait':
        return static::_trait();
      case 'try':
        return static::_try();
      case 'unset':
        return static::_unset();
      case 'use':
        return static::_use();
      case 'var':
        return static::_var();
      case 'while':
        return static::_while();
      case 'yield':
        return static::_yield();
      case 'and':
        return static::logicalAnd();
      case 'or':
        return static::logicalOr();
      case 'xor':
        return static::logicalXor();
      case '(array)':
        return static::arrayCast();
      case '(bool)':
      case '(boolean)':
        return static::booleanCast();
      case '(real)':
      case '(double)':
      case '(float)':
        return static::doubleCast();
      case '(int)':
      case '(integer)':
        return static::integerCast();
      case '(object)':
        return static::objectCast();
      case '(string)':
        return static::stringCast();
      case '(unset)':
        return static::unsetCast();
      case '__halt_compiler':
        return static::haltCompiler();
      case '__CLASS__':
        return static::classConstant();
      case '__DIR__':
        return static::dirConstant();
      case '__FILE__':
        return static::fileConstant();
      case '__FUNCTION__':
        return static::functionConstant();
      case '__LINE__':
        return static::lineConstant();
      case '__METHOD__':
        return static::methodConstant();
      case '__NAMESPACE__':
        return static::namespaceConstant();
      case '__TRAIT__':
        return static::traitConstant();
      case '...':
        return static::splat();
      case '=>':
        return static::doubleArrow();
      case '->':
        return static::objectOperator();
      case '::':
        return static::doubleColon();
      case '&=':
        return static::bitwiseAndAssign();
      case '|=':
        return static::bitwiseOrAssign();
      case '^=':
        return static::bitwiseXorAssign();
      case '*=':
        return static::multiplyAssign();
      case '/=':
        return static::divideAssign();
      case '%=':
        return static::modulusAssign();
      case '+=':
        return static::addAssign();
      case '-=':
        return static::subtractAssign();
      case '.=':
        return static::concatAssign();
      case '===':
        return static::isIdentical();
      case '==':
        return static::isEqual();
      case '!==':
        return static::isNotIdentical();
      case '!=':
        return static::isNotEqual();
      case '<=':
        return static::isLessThanOrEqual();
      case '>=':
        return static::isGreaterThanOrEqual();
      case '&&':
        return static::booleanAnd();
      case '||':
        return static::booleanOr();
      case '<<':
        return static::bitwiseShiftLeft();
      case '<<=':
        return static::bitwiseShiftLeftAssign();
      case '>>':
        return static::bitwiseShiftRight();
      case '>>=':
        return static::bitwiseShiftRightAssign();
      case '\\':
        return static::namespaceSeparator();
      case '--':
        return static::decrement();
      case '++':
        return static::increment();
      case '`':
      case '~':
      case '!':
      case '@':
      case '%':
      case '^':
      case '&':
      case '*':
      case '(':
      case ')':
      case '-':
      case '+':
      case '=':
      case '{':
      case '}':
      case '[':
      case ']':
      case '|':
      case ':':
      case ';':
      case "'":
      case '"':
      case '<':
      case ',':
      case '>':
      case '.':
      case '?':
      case '/':
        return new TokenNode($text, $text);
      case ' ':
        return static::space();
      case '?>':
        return static::closeTag();
      default:
        if (rtrim($text) === '<?php') {
          return static::openTag();
        }
        elseif (preg_match($int_regex, $text)) {
          return static::integer($text);
        }
        elseif (preg_match($decimal_regex, $text)) {
          return static::decimalNumber($text);
        }
        elseif (preg_match($var_regex, $text)) {
          return static::variable($text);
        }
        // @todo handle all tokens as per http://php.net/manual/en/tokens.php
        throw new \InvalidArgumentException("Unable to parse '{$text}'");
    }
  }

  public static function _abstract() {
    return new TokenNode(T_ABSTRACT, 'abstract');
  }

  public static function add() {
    return new TokenNode('+', '+');
  }

  public static function addAssign() {
    return new TokenNode(T_PLUS_EQUAL, '+=');
  }

  public static function _array() {
    return new TokenNode(T_ARRAY, 'array');
  }

  public static function arrayCast() {
    return new TokenNode(T_ARRAY_CAST, '(array)');
  }

  public static function _as() {
    return new TokenNode(T_AS, 'as');
  }

  public static function assign() {
    return new TokenNode('=', '=');
  }

  public static function backtick() {
    return new TokenNode('`', '`');
  }

  public static function bitwiseAnd() {
    return new TokenNode('&', '&');
  }

  public static function bitwiseAndAssign() {
    return new TokenNode(T_AND_EQUAL, '&=');
  }

  public static function bitwiseNot() {
    return new TokenNode('~', '~');
  }

  public static function bitwiseOr() {
    return new TokenNode('|', '|');
  }

  public static function bitwiseOrAssign() {
    return new TokenNode(T_OR_EQUAL, '|=');
  }

  public  static function bitwiseXor() {
    return new TokenNode('^', '^');
  }

  public static function bitwiseXorAssign() {
    return new TokenNode(T_XOR_EQUAL, '^=');
  }

  public static function bitwiseShiftLeft() {
    return new TokenNode(T_SL, '<<');
  }

  public static function bitwiseShiftLeftAssign() {
    return new TokenNode(T_SL_EQUAL, '<<=');
  }

  public static function bitwiseShiftRight() {
    return new TokenNode(T_SR, '>>');
  }

  public static function bitwiseShiftRightAssign() {
    return new TokenNode(T_SR_EQUAL, '>>=');
  }

  public static function booleanAnd() {
    return new TokenNode(T_BOOLEAN_AND, '&&');
  }

  public static function booleanOr() {
    return new TokenNode(T_BOOLEAN_OR, '||');
  }

  public static function booleanCast() {
    return new TokenNode(T_BOOL_CAST, '(boolean)');
  }

  public static function _break() {
    return new TokenNode(T_BREAK, 'break');
  }

  public static function _callable() {
    return new TokenNode(T_CALLABLE, 'callable');
  }

  public static function _case() {
    return new TokenNode(T_CASE, 'case');
  }

  public static function _catch() {
    return new TokenNode(T_CATCH, 'catch');
  }

  public static function _class() {
    return new TokenNode(T_CLASS, 'class');
  }

  public static function classConstant() {
    return new ClassMagicConstantNode(T_CLASS_C, '__CLASS__');
  }

  public static function _clone() {
    return new TokenNode(T_CLONE, 'clone');
  }

  public static function closeTag() {
    return new TokenNode(T_CLOSE_TAG, '?>');
  }

  public static function colon() {
    return new TokenNode(':', ':');
  }

  public static function comma() {
    return new TokenNode(',', ',');
  }

  public static function concat() {
    return new TokenNode('.', '.');
  }

  public static function concatAssign() {
    return new TokenNode(T_CONCAT_EQUAL, '.=');
  }

  public static function _const() {
    return new TokenNode(T_CONST, 'const');
  }

  public static function _continue() {
    return new TokenNode(T_CONTINUE, 'continue');
  }

  public static function curlyOpen() {
    return new TokenNode(T_CURLY_OPEN, '{');
  }

  public static function decrement() {
    return new TokenNode(T_DEC, '--');
  }

  public static function _declare() {
    return new TokenNode(T_DECLARE, 'declare');
  }

  public static function _default() {
    return new TokenNode(T_DEFAULT, 'default');
  }

  public static function dirConstant() {
    return new DirMagicConstantNode(T_DIR, '__DIR__');
  }

  public static function _die() {
    return new TokenNode(T_EXIT, 'die');
  }

  public static function divide() {
    return new TokenNode('/', '/');
  }

  public static function divideAssign() {
    return new TokenNode(T_DIV_EQUAL, '/=');
  }

  public static function decimalNumber($number) {
    return new FloatNode(T_DNUMBER, $number);
  }

  public static function _do() {
    return new TokenNode(T_DO, 'do');
  }

  public static function dollarOpenCurly() {
    return new TokenNode(T_DOLLAR_OPEN_CURLY_BRACES, '${');
  }

  public static function doubleArrow() {
    return new TokenNode(T_DOUBLE_ARROW, '=>');
  }

  public static function doubleCast() {
    return new TokenNode(T_DOUBLE_CAST, '(double)');
  }

  public static function doubleColon() {
    return new TokenNode(T_DOUBLE_COLON, '::');
  }

  public static function _echo() {
    return new TokenNode(T_ECHO, 'echo');
  }

  public static function _else() {
    return new TokenNode(T_ELSE, 'else');
  }

  public static function _elseIf() {
    return new TokenNode(T_ELSEIF, 'elseif');
  }

  public static function _empty() {
    return new TokenNode(T_EMPTY, 'empty');
  }

  public static function _endDeclare() {
    return new TokenNode(T_ENDDECLARE, 'enddeclare');
  }

  public static function _endFor() {
    return new TokenNode(T_ENDFOR, 'endfor');
  }

  public static function _endForeach() {
    return new TokenNode(T_ENDFOREACH, 'endforeach');
  }

  public static function _endIf() {
    return new TokenNode(T_ENDIF, 'endif');
  }

  public static function _endSwitch() {
    return new TokenNode(T_ENDSWITCH, 'endswitch');
  }

  public static function _endWhile() {
    return new TokenNode(T_ENDWHILE, 'endwhile');
  }

  public static function _eval() {
    return new TokenNode(T_EVAL, 'eval');
  }

  public static function _exit() {
    return new TokenNode(T_EXIT, 'exit');
  }

  public static function _extends() {
    return new TokenNode(T_EXTENDS, 'extends');
  }

  public static function fileConstant() {
    return new FileMagicConstantNode(T_FILE, '__FILE__');
  }

  public static function _final() {
    return new TokenNode(T_FINAL, 'final');
  }

  public static function _finally() {
    return new TokenNode(T_FINALLY, 'finally');
  }

  public static function _for() {
    return new TokenNode(T_FOR, 'for');
  }

  public static function _foreach() {
    return new TokenNode(T_FOREACH, 'foreach');
  }

  public static function _function() {
    return new TokenNode(T_FUNCTION, 'function');
  }

  public static function functionConstant() {
    return new FunctionMagicConstantNode(T_FUNC_C, '__FUNCTION__');
  }

  public static function _global() {
    return new TokenNode(T_GLOBAL, 'global');
  }

  public static function _goto() {
    return new TokenNode(T_GOTO, 'goto');
  }

  public static function haltCompiler() {
    return new TokenNode(T_HALT_COMPILER, '__halt_compiler');
  }

  public static function identifier($id) {
    return new TokenNode(T_STRING, $id);
  }

  public static function _if() {
    return new TokenNode(T_IF, 'if');
  }

  public static function _implements() {
    return new TokenNode(T_IMPLEMENTS, 'implements');
  }

  public static function increment() {
    return new TokenNode(T_INC, '++');
  }

  public static function _include() {
    return new TokenNode(T_INCLUDE, 'include');
  }

  public static function _includeOnce() {
    return new TokenNode(T_INCLUDE_ONCE, 'include_once');
  }

  public static function inlineHtml($html) {
    return new TokenNode(T_INLINE_HTML, $html);
  }

  public static function _instanceOf() {
    return new TokenNode(T_INSTANCEOF, 'instanceof');
  }

  public static function _insteadOf() {
    return new TokenNode(T_INSTEADOF, 'insteadof');
  }

  public static function integerCast() {
    return new TokenNode(T_INT_CAST, '(integer)');
  }

  public static function _interface() {
    return new TokenNode(T_INTERFACE, 'interface');
  }

  public static function _isset() {
    return new TokenNode(T_ISSET, 'isset');
  }

  public static function isEqual() {
    return new TokenNode(T_IS_EQUAL, '==');
  }

  public static function isGreaterThan() {
    return new TokenNode('>', '>');
  }

  public static function isGreaterThanOrEqual() {
    return new TokenNode(T_IS_GREATER_OR_EQUAL, '>=');
  }

  public static function isIdentical() {
    return new TokenNode(T_IS_IDENTICAL, '===');
  }

  public static function isNotEqual() {
    return new TokenNode(T_IS_NOT_EQUAL, '!=');
  }

  public static function isNotIdentical() {
    return new TokenNode(T_IS_NOT_IDENTICAL, '!==');
  }

  public static function isLessThan() {
    return new TokenNode('<', '<');
  }

  public static function isLessThanOrEqual() {
    return new TokenNode(T_IS_SMALLER_OR_EQUAL, '<=');
  }

  public static function lineConstant() {
    return new LineMagicConstantNode(T_LINE, '__LINE__');
  }

  public static function _list() {
    return new TokenNode(T_LIST, 'list');
  }

  public static function integer($number) {
    return new IntegerNode(T_LNUMBER, $number);
  }

  public static function logicalAnd() {
    return new TokenNode(T_LOGICAL_AND, 'and');
  }

  public static function logicalOr() {
    return new TokenNode(T_LOGICAL_OR, 'or');
  }

  public static function logicalXor() {
    return new TokenNode(T_LOGICAL_XOR, 'xor');
  }

  public static function methodConstant() {
    return new MethodMagicConstantNode(T_METHOD_C, '__METHOD__');
  }

  public static function modulus() {
    return new TokenNode('%', '%');
  }

  public static function modulusAssign() {
    return new TokenNode(T_MOD_EQUAL, '%=');
  }

  public static function multiply() {
    return new TokenNode('*', '*');
  }

  public static function multiplyAssign() {
    return new TokenNode(T_MUL_EQUAL, '*=');
  }

  public static function _namespace() {
    return new TokenNode(T_NAMESPACE, 'namespace');
  }

  public static function namespaceConstant() {
    return new NamespaceMagicConstantNode(T_NS_C, '__NAMESPACE__');
  }

  public static function namespaceSeparator() {
    return new TokenNode(T_NS_SEPARATOR, '\\');
  }

  public static function _new() {
    return new TokenNode(T_NEW, 'new');
  }

  public static function newline() {
    return Token::whitespace("\n");
  }

  public static function not() {
    return new TokenNode('!', '!');
  }

  public static function numString($index) {
    return new TokenNode(T_NUM_STRING, $index);
  }

  public static function objectCast() {
    return new TokenNode(T_OBJECT_CAST, '(object)');
  }

  public static function objectOperator() {
    return new TokenNode(T_OBJECT_OPERATOR, '->');
  }

  public static function openTag() {
    return new TokenNode(T_OPEN_TAG, '<?php' . "\n");
  }

  public static function openEchoTag() {
    return new TokenNode(T_OPEN_TAG_WITH_ECHO, '<?=');
  }

  public static function _print() {
    return new TokenNode(T_PRINT, 'print');
  }

  public static function _public() {
    return new TokenNode(T_PUBLIC, 'public');
  }

  public static function _protected() {
    return new TokenNode(T_PROTECTED, 'protected');
  }

  public static function _private() {
    return new TokenNode(T_PRIVATE, 'private');
  }

  public static function reference() {
    return new TokenNode('&', '&');
  }

  public static function _require() {
    return new TokenNode(T_REQUIRE, 'require');
  }

  public static function _requireOnce() {
    return new TokenNode(T_REQUIRE_ONCE, 'require_once');
  }

  public static function _return() {
    return new TokenNode(T_RETURN, 'return');
  }

  public static function semiColon() {
    return new TokenNode(';', ';');
  }

  public static function splat() {
    return new TokenNode(T_ELLIPSIS, '...');
  }

  public static function subtract() {
    return new TokenNode('-', '-');
  }

  public static function subtractAssign() {
    return new TokenNode(T_MINUS_EQUAL, '-=');
  }

  public static function space() {
    return Token::whitespace(' ');
  }

  public static function _static() {
    return new TokenNode(T_STATIC, 'static');
  }

  public static function stringCast() {
    return new TokenNode(T_STRING_CAST, '(string)');
  }

  public static function suppress() {
    return new TokenNode('@', '@');
  }

  public static function _switch() {
    return new TokenNode(T_SWITCH, 'switch');
  }

  public static function ternaryOperator() {
    return new TokenNode('?', '?');
  }

  public static function _throw() {
    return new TokenNode(T_THROW, 'throw');
  }

  public static function _trait() {
    return new TokenNode(T_TRAIT, 'trait');
  }

  public static function traitConstant() {
    return new TraitMagicConstantNode(T_TRAIT_C, '__TRAIT__');
  }

  public static function _try() {
    return new TokenNode(T_TRY, 'try');
  }

  public static function _unset() {
    return new TokenNode(T_UNSET, 'unset');
  }

  public static function unsetCast() {
    return new TokenNode(T_UNSET_CAST, '(unset)');
  }

  public static function _use() {
    return new TokenNode(T_USE, 'use');
  }

  public static function _var() {
    return new TokenNode(T_VAR, 'var');
  }

  public static function variable($var) {
    return new VariableNode(T_VARIABLE, $var);
  }

  public static function _while() {
    return new TokenNode(T_WHILE, 'while');
  }

  public static function whitespace($ws) {
    return WhitespaceNode::create($ws);
  }

  public static function _yield() {
    return new TokenNode(T_YIELD, 'yield');
  }

  public static function openBrace() {
    return new TokenNode('{', '{');
  }

  public static function closeBrace() {
    return new TokenNode('}', '}');
  }

  public static function openBracket() {
    return new TokenNode('[', '[');
  }

  public static function closeBracket() {
    return new TokenNode(']', ']');
  }

  public static function openParen() {
    return new TokenNode('(', '(');
  }

  public static function closeParen() {
    return new TokenNode(')', ')');
  }

  public static function startHeredoc($label) {
    return new TokenNode(T_START_HEREDOC, "<<<{$label}\n");
  }

  public static function endHeredoc($label) {
    return new TokenNode(T_END_HEREDOC, $label);
  }

  public static function startNowdoc($label) {
    return new TokenNode(T_START_HEREDOC,  "<<<'{$label}'\n");
  }

  public static function endNowdoc($label) {
    return static::endHeredoc($label);
  }

  public static function doubleQuote() {
    return new TokenNode('"', '"');
  }
}
