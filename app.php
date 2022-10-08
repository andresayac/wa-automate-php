<?php
require "lib/Whatsapp.class.php";



$wa_automate = new Whatsapp();

// $bot->getScreenShot();

$qr_whatsapp = $wa_automate->getQrCode();
echo $wa_automate->getQrTerminal($qr_whatsapp);
