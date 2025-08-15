<?php

use Behat\Behat\Context\Context;
use Facebook\WebDriver\WebDriverBy;
use App\WebDriverFactory;
use PHPUnit\Framework\Assert;

class LoginContext implements Context
{
    private $driver;

    public function __construct()
    {
        $this->driver = WebDriverFactory::getDriver();
    }

    /** @Given /^User is on the login page$/ */
    public function userIsOnTheLoginPage()
    {
        $this->driver->get('https://www.saucedemo.com');
    }

    /** @When /^User enters username "([^"]*)" and password "([^"]*)"$/ */
    public function userEntersUsernameAndPassword($username, $password)
    {
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys($username);
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys($password);
    }

    /** @Then /^User clicks on Login button$/ */
    public function userClicksOnLoginButton()
    {
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();
    }

    /** @Then /^User should see "([^"]*)"$/ */
    public function userShouldSee($expectedMessage)
    {
        $errorMessage = $this->driver
            ->findElement(WebDriverBy::cssSelector("h3[data-test='error']"))
            ->getText();
        Assert::assertEquals($expectedMessage, $errorMessage);
    }

    /** @Given /^User is logged in as "([^"]*)" with password "([^"]*)"$/ */
    public function userIsLoggedInAsWithPassword($username, $password)
    {
        $this->driver->get('https://www.saucedemo.com');
        $this->driver->findElement(WebDriverBy::id('user-name'))->sendKeys($username);
        $this->driver->findElement(WebDriverBy::id('password'))->sendKeys($password);
        $this->driver->findElement(WebDriverBy::id('login-button'))->click();
    }

    /** @AfterScenario */
    public function tearDown()
    {
        WebDriverFactory::quitDriver();
    }
}
