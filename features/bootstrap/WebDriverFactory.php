<?php
namespace App;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;

class WebDriverFactory
{
    private static $driver;

    public static function getDriver(bool $headless = false) // toggle headless here
    {
        if (!self::$driver) {
            $host = 'http://localhost:4444';

            $options = new ChromeOptions();

            // Disable Chrome password save popups
            $options->setExperimentalOption('prefs', [
                'credentials_enable_service' => false,
                'profile.password_manager_enabled' => false,
                'profile.password_manager_leak_detection' => false
            ]);

            // Chrome arguments
            $chromeArgs = [
                '--disable-notifications',
                '--disable-infobars',
                '--start-maximized'
            ];

            if ($headless) {
                $chromeArgs[] = '--headless=new'; // new headless mode for Chrome >=109
                $chromeArgs[] = '--window-size=1920,1080';
            }

            $options->addArguments($chromeArgs);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

            self::$driver = RemoteWebDriver::create($host, $capabilities);
        }
        return self::$driver;
    }

    public static function quitDriver()
    {
        if (self::$driver) {
            self::$driver->quit();
            self::$driver = null;
        }
    }
}