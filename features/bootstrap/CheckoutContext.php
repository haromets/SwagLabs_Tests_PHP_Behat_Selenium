<?php

use Behat\Behat\Context\Context;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use App\WebDriverFactory;
use PHPUnit\Framework\Assert;

class CheckoutContext implements Context
{
    private $driver;
    private $itemName = [];
    private $itemPrice = [];

    public function __construct()
    {
        $this->driver = WebDriverFactory::getDriver();
    }

    /** @When /^User adds "([^"]*)" to the cart$/ */
    public function userAddsToTheCart($productName)
    {
        $this->itemName[] = $productName;

        $xpathPrice = "//div[@class='inventory_item'][.//div[text()='{$productName}']]//div[@class='inventory_item_price']";
        $priceText = $this->driver->findElement(WebDriverBy::xpath($xpathPrice))->getText();
        $this->itemPrice[] = (float)str_replace('$', '', $priceText);

        $xpath = "//div[@class='inventory_item'][.//div[text()='{$productName}']]//button";
        $this->driver->findElement(WebDriverBy::xpath($xpath))->click();
    }

    /** @Then /^cart icon has total amount "([^"]*)"$/ */
    public function cartIconHasTotalAmount($amountOfItems)
    {
        $wait = new WebDriverWait($this->driver, 5);
        $wait->until(
            WebDriverExpectedCondition::textToBePresentInElement(
                WebDriverBy::className('shopping_cart_badge'),
                $amountOfItems
            )
        );

        $totalAmountOfItems = $this->driver->findElement(WebDriverBy::className('shopping_cart_badge'))->getText();
        Assert::assertEquals(
            $amountOfItems,
            $totalAmountOfItems,
            'The amount of items in the cart does not match the expected total!'
        );
    }

    /** @When /^User clicks on cart icon to proceeds to checkout$/ */
    public function userClicksOnCartIconToProceedsToCheckout()
    {
        $this->driver->findElement(WebDriverBy::className('shopping_cart_link'))->click();
    }

    /** @Given /^User is on the cart page$/ */
    public function userIsOnTheCartPage()
    {
        $expectedUrl = 'https://www.saucedemo.com/cart.html';
        $actualUrl = $this->driver->getCurrentURL();

        if ($actualUrl !== $expectedUrl) {
            throw new \RuntimeException(
                "User is not on the cart page. Expected: {$expectedUrl} but was: {$actualUrl}"
            );
        }
    }

    /** @Then /^verify items and prices on checkout step$/ */
    public function verifyItemsAndPricesOnCheckoutStep()
    {
        $nameElements = $this->driver->findElements(WebDriverBy::xpath("//div[@class='cart_item']//div[@class='inventory_item_name']"));
        $priceElements = $this->driver->findElements(WebDriverBy::xpath("//div[@class='cart_item']//div[@class='inventory_item_price']"));

        $actualNames = array_map(fn($el) => $el->getText(), $nameElements);
        $actualPrices = array_map(fn($el) => (float)str_replace('$', '', trim($el->getText())), $priceElements);

        foreach ($this->itemName as $i => $expectedName) {
            $expectedPrice = $this->itemPrice[$i];
            Assert::assertEquals(
                $expectedName,
                $actualNames[$i],
                "Mismatch in product name at index {$i} | Expected: {$expectedName} | Actual: {$actualNames[$i]}"
            );
            Assert::assertEquals(
                $expectedPrice,
                $actualPrices[$i],
                "Mismatch in product price at index {$i} ({$expectedName}) | Expected: {$expectedPrice} | Actual: {$actualPrices[$i]}"
            );
        }
    }

    /** @Then /^User clicks on Checkout button$/ */
    public function userClicksOnCheckoutButton()
    {
        $this->driver->findElement(WebDriverBy::id('checkout'))->click();
    }

    /** @When /^User enters First Name "([^"]*)", Last Name "([^"]*)" and Postal Code "([^"]*)" and clicks Continue button$/ */
    public function userEntersFirstNameLastNameAndPostalCodeAndClicksContinueButton($firstName, $lastName, $postalCode)
    {
        $wait = new WebDriverWait($this->driver, 5);
        sleep(1);
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('first-name')))->sendKeys($firstName);
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('last-name')))->sendKeys($lastName);
        $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('postal-code')))->sendKeys($postalCode);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('continue')))->click();
    }

    /** @Then /^verify the total price without tax$/ */
    public function verifyTheTotalPriceWithoutTax()
    {
        $actualTotalPrice = array_sum($this->itemPrice);
        $totalPriceText = $this->driver->findElement(WebDriverBy::className('summary_subtotal_label'))->getText();
        $expectedTotalPrice = (float)str_replace('Item total: $', '', $totalPriceText);

        Assert::assertEquals(
            $expectedTotalPrice,
            $actualTotalPrice,
            'Total price does not match'
        );
    }

    /** @Then /^verify the final price including tax$/ */
    public function verifyTheFinalPriceIncludingTax()
    {
        $subtotal = (float)str_replace('Item total: $', '', $this->driver->findElement(WebDriverBy::className('summary_subtotal_label'))->getText());
        $tax = (float)str_replace('Tax: $', '', $this->driver->findElement(WebDriverBy::className('summary_tax_label'))->getText());
        $total = (float)str_replace('Total: $', '', $this->driver->findElement(WebDriverBy::className('summary_total_label'))->getText());

        $expectedTotal = $subtotal + $tax;
        Assert::assertEquals(
            $expectedTotal,
            $total,
            'Final price including tax does not match',
            0.01
        );
    }

    /** @When /^User clicks on Finish button$/ */
    public function userClicksOnFinishButton()
    {
        $this->driver->findElement(WebDriverBy::id('finish'))->click();
    }

    /** @Then /^verify the order should be completed successfully$/ */
    public function verifyTheOrderShouldBeCompletedSuccessfully()
    {
        $completeHeader = $this->driver->findElement(WebDriverBy::className('complete-header'))->getText();
        Assert::assertEquals('Thank you for your order!', $completeHeader);
    }

    /** @AfterScenario */
    public function tearDown()
    {
        WebDriverFactory::quitDriver();
    }
}
