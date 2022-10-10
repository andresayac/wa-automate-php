<?php

require 'vendor/autoload.php';
require_once 'src/Client.php';

$cliente = new Client();

$cliente->on('inject_wpp', function ($status) {
    echo "INJECT WPP: {$status} \n";
});

$cliente->on('authenticated', function ($status) {
    echo "AUTHENTICATION STATUS: {$status}\n";
});

$cliente->on('qr', function ($qr, $retry) {
    echo "QR RECEIVED: [{$retry}/5]\n";
    echo $qr . PHP_EOL;
});


$cliente->initialize();
