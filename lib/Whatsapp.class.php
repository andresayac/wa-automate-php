<?php

require 'vendor/autoload.php';

use HeadlessChromium\BrowserFactory;
use Simplusid\QRCode;
use HeadlessChromium\Page;

class Whatsapp
{

    public $browser, $whatsapp_page;

    function __construct()
    {
        $browserFactory = new BrowserFactory("C:\Program Files\Google\Chrome\Application\chrome.exe");
        // starts headless chrome
        $this->browser = $browserFactory->createBrowser([
            'headless' => false, // disable headless mode
            'keepAlive' => true,
        ]);
        $this->whatsapp_page = $this->browser->createPage();
        $this->whatsapp_page->navigate('https://web.whatsapp.com/')->waitForNavigation();
        $this->init();
    }

    function init()
    {
        $this->whatsapp_page->evaluate('document.title')->getReturnValue();
    }

    function getQrCode()
    {
        return $this->whatsapp_page->evaluate("document.querySelector('[data-testid=\"qrcode\"]').getAttribute('data-ref')")->getReturnValue();
    }

    function getScreenShot(string $path = '/')
    {
        $path = $path ?? '/';
        $this->whatsapp_page->screenshot()->saveToFile('screenshot.png');
    }

    function getQrTerminal($qr_whatsapp)
    {
        return QRCode::terminal($qr_whatsapp);
    }

    function __destruct()
    {
        $this->browser->close();
    }
}
