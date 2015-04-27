<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Pharborist\FileUtil;
use Pharborist\Parser;
use Pharborist\FormatterFactory;

function formatFile($filename) {
  $formatter = FormatterFactory::getDefaultFormatter();
  $tree = Parser::parseFile($filename);
  $formatter->format($tree);
  file_put_contents($filename, $tree->getText());
}

$directory = $argv[1];

$files = FileUtil::findFiles($directory);
foreach ($files as $filename) {
  formatFile($filename);
}
