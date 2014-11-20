<?php
namespace Pharborist;

/**
 * Provides settings.
 *
 * @todo Replace with proper configuration system.
 */
class Settings {
  private static $settings = array();

  public static function get($key, $default = NULL) {
    return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
  }

  public static function set($key, $value) {
    return self::$settings[$key] = $value;
  }
}

Settings::set('formatter.nl', "\n");
Settings::set('formatter.indent', '  ');
Settings::set('formatter.soft_limit', 80);
Settings::set('formatter.boolean_null.upper', TRUE);
