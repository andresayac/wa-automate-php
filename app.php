<?php
require "lib/Whatsapp.class.php";



$bot = new Whatsapp();


// $bot->getScreenShot();

$qr_whatsapp = $bot->getQrCode();
//echo $bot->getQrTerminal($qr_whatsapp);
