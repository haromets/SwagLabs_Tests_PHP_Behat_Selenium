Feature: Login Functionality Tests

  @Test01
  Scenario Outline: Login with invalid credentials
    Given User is on the login page
    When User enters username "<username>" and password "<password>"
    Then User clicks on Login button
    And User should see "<error_message>"

    Examples:
      | username        | password      | error_message                                                             |
      | not_valid_user  | secret_sauce  | Epic sadface: Username and password do not match any user in this service |
      | standard_user   | secret_sauce1 | Epic sadface: Username and password do not match any user in this service |
      | not_valid_user  | secret_sauce1 | Epic sadface: Username and password do not match any user in this service |
      | standard_user   |               | Epic sadface: Password is required                                        |
      | locked_out_user | secret_sauce  | Epic sadface: Sorry, this user has been locked out.                       |
      | locked_out_user |               | Epic sadface: Password is required                                        |
