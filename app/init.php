<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require __DIR__ . '/../vendor/autoload.php';

define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

$app = new Main(PORT);
