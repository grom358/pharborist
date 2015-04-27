<?php
namespace Pharborist;

/**
 * Formatter factory.
 */
class FormatterFactory {

  /**
   * @var Formatter
   */
  protected static $defaultFormatter;

  /**
   * Get the default formatter.
   *
   * The default formatter is used by node builders.
   *
   * @return Formatter
   */
  public static function getDefaultFormatter() {
    if (!static::$defaultFormatter) {
      static::$defaultFormatter = static::getDrupalFormatter();
    }
    return static::$defaultFormatter;
  }

  /**
   * Set the default formatter.
   *
   * @param Formatter $formatter
   */
  public static function setDefaultFormatter(Formatter $formatter) {
    static::$defaultFormatter = $formatter;
  }

  /**
   * Create formatter using the specified config file.
   *
   * @param string $filename
   *
   * @return Formatter
   */
  public static function createFormatter($filename) {
    $config = json_decode(file_get_contents($filename), TRUE);
    return new Formatter($config['formatter']);
  }

  public static function getDrupalFormatter() {
    return static::createFormatter(dirname(__DIR__) . '/config/drupal.json');
  }

  public static function getPsr2Formatter() {
    return static::createFormatter(dirname(__DIR__) . '/config/psr2.json');
  }

  /**
   * Format a node using the default formatter.
   *
   * @param Node $node
   *   Node to format.
   */
  public static function format(Node $node) {
    static::getDefaultFormatter()->format($node);
  }
}
