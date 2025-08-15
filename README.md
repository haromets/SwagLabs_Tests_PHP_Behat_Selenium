# Swag Labs Test Automation Suite

This project contains a test automation suite for the Swag Labs demo website (https://www.saucedemo.com) using PHP, Behat, and Selenium for browser automation. The suite tests login functionality, product sorting, and the checkout process as per the provided requirements.

## Prerequisites

To run this test suite, ensure you have the following installed:

- **PHP**: Version 8.1 or higher
- **Composer**: For dependency management
- **Chrome Browser**: For browser automation
- **ChromeDriver**: Compatible with your installed Chrome version

## Setup Instructions

1. **Clone the Repository**  
   Clone this repository to your local machine:

   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. **Install Dependencies**  
   Run the following command to install the required PHP dependencies:

   ```bash
   composer install
   ```

3. **Install ChromeDriver (Mac)**  
   Install ChromeDriver using Homebrew:

   ```bash
   brew install chromedriver
   ```

4. **Start ChromeDriver**  
   Start ChromeDriver on the default port:

   ```bash
   chromedriver --port=4444
   ```

5. **Configure Behat**  
   The `behat.yml` configuration is already set up in the project root. Ensure the Selenium server URL (`http://localhost:4444`) is correct in `WebDriverFactory.php` if your setup differs.

6. **Directory Structure**
   - `features/`: Contains Behat feature files written in Gherkin syntax.
   - `features/bootstrap/`: Contains context classes (`LoginContext.php`, `SortingContext.php`, `CheckoutContext.php`) with step definitions.
   - `composer.json`: Defines project dependencies and autoloading.

## Running the Tests

1. **Run All Tests**  
   Execute the entire test suite:

   ```bash
   vendor/bin/behat
   ```

2. **Run Specific Tests**  
   To run a specific feature or scenario, use tags (e.g., `@Test01` for login tests):

   ```bash
   vendor/bin/behat --tags=@Test01
   ```

3. **Headless Mode**  
   By default, tests run in headfull (visible) mode. To run in headless mode, modify `WebDriverFactory.php` and set `$headless = true` in the `getDriver` method.

## Test Scenarios

The test suite covers the following scenarios:

1. **Login Functionality Tests** (`@Test01`)

   - Tests failed login with invalid credentials and locked-out users.
   - Verifies appropriate error messages.

2. **Product Sorting Tests** (`@Test02`)

   - Tests sorting products by name (A to Z, Z to A) and price (low to high, high to low).
   - Verifies correct sorting order.

3. **Checkout Process Tests** (`@Test03`)
   - Adds "Sauce Labs Backpack" and "Sauce Labs Bike Light" to the cart.
   - Verifies cart contents, total price (with and without tax), and completes the checkout process.
   - Validates order completion.

## Future Improvements

- Add support for other browsers (e.g., Firefox, Edge) in `WebDriverFactory`.
- Enhance reporting with screenshots for failed tests.
- Implement parallel test execution to reduce runtime.

## Defect Found in Application

- **Partially Clickable Sort Button**:
  - **Description**: The sort button (dropdown arrow) on the inventory page is only partially clickable, preventing the display of sorting options.
  - **Steps to Reproduce**:
    1. Log in successfully with valid credentials and navigate to the products page.
    2. Attempt to sort products by clicking the arrow on the sort button.
  - **Expected Result**: Sorting options (e.g., Name A to Z, Price low to high) are displayed.
  - **Actual Result**: Sorting options are not displayed due to the sort button being only partially clickable.
