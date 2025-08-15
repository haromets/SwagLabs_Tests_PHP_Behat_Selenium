Feature: Checkout Process Tests

  Background:
    Given User is logged in as "standard_user" with password "secret_sauce"

  @Test03
  Scenario: User validates order completion (backpack and bike light)
    Given User is on the products page
    When User adds "Sauce Labs Backpack" to the cart
    And User adds "Sauce Labs Bike Light" to the cart
    Then cart icon has total amount "2"
    When User clicks on cart icon to proceeds to checkout
    Then User is on the cart page
    And verify items and prices on checkout step
    Then User clicks on Checkout button
    When User enters First Name "firstName", Last Name "lastName" and Postal Code "postalCode" and clicks Continue button
    Then verify items and prices on checkout step
    And verify the total price without tax
    And verify the final price including tax
    When User clicks on Finish button
    Then verify the order should be completed successfully
