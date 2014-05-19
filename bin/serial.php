<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

$serial = new PhpSerial;
$serial->deviceSet(PORT);
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(2);
$serial->confFlowControl("none");
$serial->deviceOpen('r+b');

echo 'pressione a tecla P na balanca para efetuar a leitura: ', PHP_EOL;

while (true) {
    $serial->sendMessage(chr(04) . chr(05) . ' ');
    $read = $serial->readPort();

    if (strlen($read) !== 0) {
        echo $read;
    }
}
