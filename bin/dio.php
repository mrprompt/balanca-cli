<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require __DIR__ . '/../vendor/autoload.php';

use \serial\serial as serial;

define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

// Open serial connection
$serial = new serial(PORT, O_RDWR);
$serial->set_options(array(
    "baud"   => 9600,
    "bits"   => 8,
    "stop"   => 2,
    "parity" => 0,
));

echo 'pressione a tecla P na balanca para efetuar a leitura: ', PHP_EOL;

while (true) {
    echo '.';

    $write = $serial->write(chr(0x04) . chr(0x05));

    if (isset($write) && strlen($write) != 0) {
	echo 'write: ';
	var_dump($write);
    }

    $read  = $serial->read();
    if (isset($read) && strlen($read) != 0) {
	echo 'read: ';
        var_dump($read);
    }
}
