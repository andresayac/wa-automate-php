<?php

require 'vendor/autoload.php';

use \Simplusid\QRCode;

/* $qrcode = QRCode::terminal(
    '2@Bob6YEyKPk/XgXxjsnSEC4fcwZmBKhn2N53+ZtF6wE5HydXjDnG0qyT1khD14aon13ykiv6a8+wFsA==,YlKqVR6V+QB1hMxt184BZd26UT/FCGhqa7b3RkgRjRg=,03T/3GeLZgF9bFk16/emyVfk96Ki3husXFACQ/ergQo=,fCn+sJt7jW+GGHSP/7Jk3/qtud95QXSYJGTsk/b/bmo=',
    null,
    6,
    6

);

echo($qrcode);
 */

use PHPQRCode\QRencode;
use PHPQRCode\Constants;


$text = '2@Bob6YEyKPk/XgXxjsnSEC4fcwZmBKhn2N53+ZtF6wE5HydXjDnG0qyT1khD14aon13ykiv6a8+wFsA==,YlKqVR6V+QB1hMxt184BZd26UT/FCGhqa7b3RkgRjRg=,03T/3GeLZgF9bFk16/emyVfk96Ki3husXFACQ/ergQo=,fCn+sJt7jW+GGHSP/7Jk3/qtud95QXSYJGTsk/b/bmo=';

$size =  3;
$margin = 4;

$backColor = "\033[40m  \033[0m";
$foreColor = "\033[47m  \033[0m";


$enc = QRencode::factory('L', $size, $margin);
$qrcode = $enc->encode($text, false);


$output = '';
foreach ($qrcode as $k => $qr) {
    $len = strlen($qr);
    $border = str_repeat($foreColor, $len + 2);
    if ($k === 0) {
        $output .= $border . "\n";
    }
    $curLine = '';
    for ($i = 0; $i < strlen($qr); $i++) {
        $curLine .= ($qr[$i] ? $backColor : $foreColor);
    }
    $output .= $foreColor . $curLine . $foreColor . "\n";

    if ($k === $len - 1) {
        $output .= $border . "\n";
    }
}

echo $output;