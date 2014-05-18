<?php
require_once 'app/Main.php';
require_once 'vendor/hyperthese/php-serial/src/PhpSerial.php';

define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

$app = new Main(PORT);

Gtk::main();