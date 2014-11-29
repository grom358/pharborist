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

  public static function getAll() {
    return self::$settings;
  }

  public static function setAll($settings) {
    self::$settings = $settings;
  }
}

Settings::set('formatter.nl', "\n");
Settings::set('formatter.indent', '  ');
Settings::set('formatter.soft_limit', 80);
Settings::set('formatter.boolean_null.upper', TRUE);
Settings::set('formatter.force_array_new_style', TRUE);
Settings::set('formatter.else_newline', TRUE);
Settings::set('formatter.declaration_brace_newline', FALSE);
Settings::set('formatter.list.keep_wrap', FALSE);
Settings::set('formatter.list.wrap_if_long', FALSE);
