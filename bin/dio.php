<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require '../vendor/autoload.php';

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
    $serial->write(chr(04) . chr(05));
    $read = $serial->read();

    if (strlen($read) >= 55) {
        $lePeso = new LePeso;
        $pesos  = $lePeso->recupera($read);
        var_dump($pesos);
    }
}
