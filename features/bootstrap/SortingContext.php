<?php

use Behat\Behat\Context\Context;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use App\WebDriverFactory;
use PHPUnit\Framework\Assert;

class SortingContext implements Context
{
    private $driver;

    public function __construct()
    {
        $this->driver = WebDriverFactory::getDriver();
    }

    /** @Given /^User is on the products page$/ */
    public function userIsOnTheProductsPage()
    {
        $expectedUrl = 'https://www.saucedemo.com/inventory.html';
        $actualUrl = $this->driver->getCurrentURL();

        if ($actualUrl !== $expectedUrl) {
            throw new \RuntimeException(
                "User is not on the products page. Expected: {$expectedUrl} but was: {$actualUrl}"
            );
        }
    }

    /** @When /^User sorts products by "([^"]*)"$/ */
    public function userSortsProductsBy($sortOption)
    {
        $sortDropdown = $this->driver->findElement(WebDriverBy::cssSelector("[data-test='product-sort-container']"));
        $select = new WebDriverSelect($sortDropdown);
        $select->selectByVisibleText($sortOption);
    }

    /** @Then /^products should be sorted by "([^"]*)" correctly$/ */
    public function productsShouldBeSortedByCorrectly($sortOption)
    {
        if (strpos($sortOption, 'Name') !== false) {
            $nameElements = $this->driver->findElements(WebDriverBy::className('inventory_item_name'));
            $actualNames = array_map(fn($el) => trim($el->getText()), $nameElements);

            $expectedNames = $actualNames;
            if (strpos($sortOption, 'A to Z') !== false) {
                sort($expectedNames, SORT_NATURAL | SORT_FLAG_CASE);
            } else {
                rsort($expectedNames, SORT_NATURAL | SORT_FLAG_CASE);
            }

            Assert::assertEquals(
                $expectedNames,
                $actualNames,
                "Product name sorting failed for: {$sortOption}"
            );

        } elseif (strpos($sortOption, 'Price') !== false) {
            $priceElements = $this->driver->findElements(WebDriverBy::className('inventory_item_price'));
            $actualPrices = array_map(fn($el) => (float)str_replace('$', '', $el->getText()), $priceElements);

            $expectedPrices = $actualPrices;
            if (strpos($sortOption, 'low to high') !== false) {
                sort($expectedPrices, SORT_NUMERIC);
            } else {
                rsort($expectedPrices, SORT_NUMERIC);
            }

            Assert::assertEquals(
                $expectedPrices,
                $actualPrices,
                "Product price sorting failed for: {$sortOption}"
            );
        }
    }

    /** @AfterScenario */
    public function tearDown()
    {
        WebDriverFactory::quitDriver();
    }
}
