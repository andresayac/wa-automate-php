<?php

require 'Constants.php';



use Evenement\EventEmitter;
use Simplusid\QRCode;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use HeadlessChromium\Page;
use React\Promise\Promise;
use React\Promise\Deferred;



class Client extends EventEmitter
{
    private $options;
    private $browser, $whatsapp_page;

    public function __construct(array $options = ['headless' => false])
    {
        $this->options = $options;
    }

    public function initialize()
    {

        // path to the file to store websocket's uri
        if (file_exists('socketFile')) {
            $socket = file_get_contents('socketFile');
            $this->browser  = BrowserFactory::connectToBrowser($socket);
        } else {
            // The browser was probably closed, start it again
            $browserFactory = new BrowserFactory("C:\Program Files\Google\Chrome\Application\chrome.exe");
            $this->browser = $browserFactory->createBrowser([
                'headless' => false, // disable headless mode
                'keepAlive' => true,
            ]);

            // save the uri to be able to connect again to browser
            file_put_contents('socketFile', $this->browser->getSocketUri(), LOCK_EX);
        }

        // starts headless chrome
        
        $this->whatsapp_page = $this->browser->createPage();

        $this->emit(Constants::INJECT_WPP, ['Injecting WPP...']);

        $this->whatsapp_page
            ->navigate(Constants::WHATSAPP_WEB_URL)
            ->waitForNavigation(Page::NETWORK_IDLE);

        $this->whatsapp_page->addScriptTag([
            'content' => file_get_contents('https://github.com/wppconnect-team/wa-js/releases/download/v2.13.0/wppconnect-wa.js')
        ])->waitForResponse(60000);

        $deferred = new Deferred();
        $deferred->promise()
            ->then(function () {
                return $this->whatsapp_page->evaluate('window.WPP?.isReady');
            })
            ->then(function ($evaluate) {
                $inject_wpp =  (!$evaluate->getReturnValue(30000)) ? 'correct injection' : 'failed injection';
                $this->emit(Constants::INJECT_WPP, [$inject_wpp]);
            });

        $deferred->resolve();

        if ($this->whatsapp_page->evaluate('window.WPP.conn.isAuthenticated()')->getReturnValue(30000)) {
            $this->emit(Constants::AUTHENTICATED, ['Authenticated']);
        } else {
            $this->emit(Constants::AUTHENTICATED, ['Not Authenticated']);

            $this->getQR();
            sleep(20);

            foreach (range(2, 4) as $i) {
                if ($this->whatsapp_page->evaluate('window.WPP.conn.isAuthenticated()')->getReturnValue(30000)) {
                    $this->emit(Constants::AUTHENTICATED, ['Authenticated']);
                    break;
                } else {
                    sleep(20);
                    $this->whatsapp_page->evaluate('window.WPP.conn.refreshQR()??false')->waitForResponse(10000);
                    $this->clearConsole();
                    $this->getQR($i);
                }
            }
        }

        if ($this->whatsapp_page->evaluate('window.WPP.conn.isAuthenticated()')->getReturnValue(30000)) {
            //
        }
    }

    private function getQR($retrys = 1)
    {

        $qr_whatsapp = $this->whatsapp_page->evaluate("document.querySelector(\"canvas[aria-label='Scan me!']\")?document.querySelector(\"canvas[aria-label='Scan me!']\").parentElement.getAttribute(\"data-ref\"):false")->getReturnValue(30000);
        if ($qr_whatsapp) {
            $qr_received = QRCode::terminal($qr_whatsapp, null, 3, 5);
            $this->emit(Constants::QR_RECEIVED, [$qr_received, $retrys]);
        }
    }

    private function clearConsole()
    {

        (in_array(PHP_OS, ['Windows', 'WIN32', 'WINNT'])) ? system('clear') : system('clear');
    }
}
