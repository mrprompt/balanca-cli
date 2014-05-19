<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require 'app/LePeso.php';
require 'app/Main.php';

define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

$app = new Main(PORT);