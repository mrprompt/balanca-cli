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
$serial->deviceOpen();
$serial->sendMessage(0x04);
$serial->sendMessage(0x05);
$read  = $serial->readPort();
var_dump($read); exit;

// $serial = new serial\serial(PORT);
// $serial->set_options(array(
//     "baud"      => 9600,
//     "bits"      => 8,
//     "stop"      => 2,
//     "parity"    => 0,
// ));
// $write = $serial->write(0x04 + 0x05);
// $read  = $serial->read();
// var_dump($read);

// $manager = new Serially\ConnectionManager;
// $connection = $manager->getConnection(PORT);
// $connection->open();
// $connection->writeLine(pack("H*", dechex(4)));
// var_dump($connection->readByte(1));

