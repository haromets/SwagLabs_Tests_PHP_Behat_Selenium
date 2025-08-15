Feature: Product Sorting Tests

  Background:
    Given User is logged in as "standard_user" with password "secret_sauce"

  @Test02
  Scenario Outline: User validates product sorting functionality
    Given User is on the products page
    When User sorts products by "<sortOption>"
    Then products should be sorted by "<sortOption>" correctly

    Examples:
      | sortOption         |
      | Name (A to Z)      |
      | Name (Z to A)      |
      | Price (low to high)|
      | Price (high to low)|
