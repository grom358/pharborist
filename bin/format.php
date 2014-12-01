<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Pharborist\SourceDiscovery;
use Pharborist\Parser;
use Pharborist\FormatterFactory;

function formatFile($filename) {
  $formatter = FormatterFactory::getDefaultFormatter();
  $tree = Parser::parseFile($filename);
  $formatter->format($tree);
  file_put_contents($filename, $tree->getText());
}

$source_discovery = new SourceDiscovery('formatFile');
$source_discovery->scanDirectory($argv[1]);
