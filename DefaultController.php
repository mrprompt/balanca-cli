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
define('PORT', '/dev/tty.usbserial');

echo 'abrindo porta' . PHP_EOL;
// $f    = fopen(PORT, "r+");

// echo 'Iniciando comunicação' . PHP_EOL;
// fwrite($f, "request");
// // fflush($f);

// echo 'aguardando...' . PHP_EOL;
// sleep(2);

// echo 'Iniciando leitura' . PHP_EOL;
// $read   = 1;
// $c      = '';

// while($read > 0) {
//     // var_dump(fread($f, 1));
//     $bytesr = unpack("h*", fread($f, 1));
//     $c     .= $bytesr[1];
//    // echo $bytesr[1];

//     if($bytesr[1] == 'ff') {
//         $read = 0;
//     }
// }

// var_dump($c);
// exit;

// Let's start the class
$serial = new PhpSerial;
$serial->deviceSet(PORT);
$serial->confBaudRate(8400);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen();

// // To write into
$serial->sendMessage("Hello !");

// // Or to read from
$read = $serial->readPort();
