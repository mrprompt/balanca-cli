#!/usr/bin/env php
<?php
/**
 * Leitura do visor da balança via porta serial
 *
 * @author Thiago Paes <mrprompt@gmail.com>
 */
require 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Porta
 *
 * OSX: /dev/tty.usbserial
 * Linux: /dev/serial or /dev/ttyS2
 * Windows: COM3
 */
define('PORT', (empty($argv[1]) ? '/dev/ttyUSB0' : $argv[1]));

$serial = new PhpSerial;
$serial->deviceSet(PORT);
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(2);
$serial->confFlowControl("none");
$serial->deviceOpen('r+b');
echo 'porta aberta, pressione a tecla P na balanca para efetuar a leitura: ', PHP_EOL;

$serial->sendMessage(pack("H*", "347"));

while (true) {
    $read = $serial->readPort();

    if (strlen($read) !== 0) {
        echo $read, PHP_EOL;
    }
}
