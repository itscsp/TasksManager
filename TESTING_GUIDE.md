# Complete Guide to Testing WordPress Plugins with PHPUnit

## Table of Contents
1. [Introduction](#introduction)
2. [Why Testing Matters](#why-testing-matters)
3. [Setting Up the Testing Environment](#setting-up-the-testing-environment)
4. [Project Structure](#project-structure)
5. [Essential Files for Testing](#essential-files-for-testing)
6. [Writing Your First Test](#writing-your-first-test)
7. [Advanced Testing Techniques](#advanced-testing-techniques)
8. [Running Tests](#running-tests)
9. [Best Practices](#best-practices)
10. [Troubleshooting Common Issues](#troubleshooting-common-issues)

## Introduction

Testing is a crucial aspect of WordPress plugin development that ensures your code works correctly, prevents regressions, and makes refactoring safer. This comprehensive guide will walk you through setting up a complete testing environment for WordPress plugins using PHPUnit and Brain\Monkey.

## Why Testing Matters

### Benefits of Testing WordPress Plugins
- **Quality Assurance**: Catch bugs before they reach production
- **Regression Prevention**: Ensure new features don't break existing functionality
- **Documentation**: Tests serve as living documentation of how your code should work
- **Refactoring Confidence**: Change code with confidence knowing tests will catch issues
- **Team Collaboration**: Tests help team members understand expected behavior

### Types of Tests
- **Unit Tests**: Test individual functions/methods in isolation
- **Integration Tests**: Test how different parts work together
- **Functional Tests**: Test complete features from user perspective

## Setting Up the Testing Environment

### Step 1: Initialize Composer

First, create a `composer.json` file in your plugin root:

```json
{
    "name": "your-username/your-plugin-name",
    "description": "WordPress plugin description",
    "type": "wordpress-plugin",
    "require": {
        "php": ">=7.4"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.0",
        "brain/monkey": "^2.6",
        "wp-coding-standards/wpcs": "^2.3",
        "dealerdirect/phpcodesniffer-composer-installer": "^0.7.0"
    },
    "autoload": {
        "psr-4": {
            "YourPlugin\\": "includes/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "YourPlugin\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "./vendor/bin/phpunit",
        "phpcs": "./vendor/bin/phpcs --standard=WordPress"
    }
}
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Key Dependencies Explained

- **PHPUnit**: The testing framework
- **Brain\Monkey**: Mocks WordPress functions for isolated testing
- **WPCS**: WordPress Coding Standards for code quality
- **Composer Installer**: Automatically installs coding standards

## Project Structure

Here's the recommended directory structure for a testable WordPress plugin:

```
your-plugin/
├── your-plugin.php              # Main plugin file
├── composer.json                # Composer configuration
├── phpunit.xml                  # PHPUnit configuration
├── .gitignore                   # Git ignore file
├── includes/                    # Main plugin classes
│   ├── class-plugin-loader.php
│   ├── class-post-type.php
│   └── class-taxonomy.php
├── admin/                       # Admin-specific functionality
│   └── class-admin.php
├── public/                      # Public-facing functionality
│   └── class-public.php
├── tests/                       # Test files
│   ├── bootstrap.php            # Test bootstrap file
│   └── unit/                    # Unit tests
│       ├── PostTypeTest.php
│       ├── TaxonomyTest.php
│       └── AdminTest.php
└── vendor/                      # Composer dependencies (ignored in git)
```

## Essential Files for Testing

### 1. PHPUnit Configuration (`phpunit.xml`)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="tests/bootstrap.php"
    backupGlobals="false"
    colors="true"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="true"
    convertWarningsToExceptions="true"
    processIsolation="false"
    stopOnFailure="false"
    verbose="true"
>
    <testsuites>
        <testsuite name="Unit Tests">
            <directory suffix="Test.php">./tests/unit</directory>
        </testsuite>
    </testsuites>
    
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./includes</directory>
            <directory suffix=".php">./admin</directory>
            <directory suffix=".php">./public</directory>
        </whitelist>
    </filter>
    
    <logging>
        <log type="coverage-html" target="./tests/coverage"/>
        <log type="coverage-text" target="php://stdout" showUncoveredFiles="false"/>
    </logging>
</phpunit>
```

### 2. Test Bootstrap File (`tests/bootstrap.php`)

```php
<?php
/**
 * PHPUnit bootstrap file
 *
 * @package YourPlugin
 */

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Initialize Brain\Monkey
Brain\Monkey\setUp();

// Define WordPress constants that might be used
if (!defined('ABSPATH')) {
    define('ABSPATH', '/path/to/wordpress/');
}

// Define plugin constants
define('YOUR_PLUGIN_DIR', dirname(__DIR__) . '/');
define('YOUR_PLUGIN_URL', 'http://blogchethanspoojarycom.local/wp-content/plugins/your-plugin/');
define('YOUR_PLUGIN_VERSION', '1.0.0');

// Mock common WordPress functions
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://blogchethanspoojarycom.local/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

// Clean up after each test
register_shutdown_function(function() {
    Brain\Monkey\tearDown();
});
```

### 3. Git Ignore File (`.gitignore`)

```
/vendor/
/tests/coverage/
.phpunit.result.cache
composer.lock
```

## Writing Your First Test

### Example Plugin Class

Let's say you have a post type class (`includes/class-post-type.php`):

```php
<?php
namespace YourPlugin;

class Post_Type {
    public function init() {
        add_action('init', array($this, 'register_post_type'));
    }

    protected function get_post_type_args() {
        return [
            'public' => true,
            'rewrite' => ['slug' => 'tasks'],
            'supports' => ['title', 'editor', 'comments'],
            'labels' => [
                'name' => 'Tasks',
                'singular_name' => 'Task'
            ]
        ];
    }

    public function register_post_type() {
        register_post_type('task', $this->get_post_type_args());
    }
}
```

### Writing the Test

Create `tests/unit/PostTypeTest.php`:

```php
<?php
namespace YourPlugin\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use YourPlugin\Post_Type;

class PostTypeTest extends TestCase {
    /**
     * Setup method - runs before each test
     */
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Teardown method - runs after each test
     */
    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test that the post type registration is called correctly
     */
    public function test_register_post_type() {
        // Arrange: Set up the mock expectation
        Monkey\Functions\expect('register_post_type')
            ->once()
            ->with('task', \Mockery::type('array'));

        // Act: Call the method we're testing
        $post_type = new Post_Type();
        $post_type->register_post_type();

        // Assert: Verify the test completed without errors
        $this->assertTrue(true);
    }

    /**
     * Test the post type arguments structure
     */
    public function test_post_type_args() {
        // Arrange: Create instance and use reflection to access protected method
        $post_type = new Post_Type();
        $reflection = new \ReflectionClass($post_type);
        $method = $reflection->getMethod('get_post_type_args');
        $method->setAccessible(true);

        // Act: Get the arguments
        $args = $method->invoke($post_type);

        // Assert: Verify the structure and values
        $this->assertIsArray($args);
        $this->assertTrue($args['public']);
        $this->assertEquals('tasks', $args['rewrite']['slug']);
        $this->assertContains('title', $args['supports']);
        $this->assertContains('editor', $args['supports']);
        $this->assertContains('comments', $args['supports']);
        $this->assertEquals('Tasks', $args['labels']['name']);
        $this->assertEquals('Task', $args['labels']['singular_name']);
    }

    /**
     * Test the init method hooks registration
     */
    public function test_init_hooks() {
        // Arrange: Mock the add_action function
        Monkey\Functions\expect('add_action')
            ->once()
            ->with('init', \Mockery::type('array'));

        // Act: Call the init method
        $post_type = new Post_Type();
        $post_type->init();

        // Assert: Verify the test completed
        $this->assertTrue(true);
    }
}
```

## Advanced Testing Techniques

### Testing Meta Boxes

```php
<?php
namespace YourPlugin\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use YourPlugin\Meta_Boxes;

class MetaBoxesTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        
        // Clear any existing $_POST data
        $_POST = [];
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_save_meta_data_with_valid_nonce() {
        // Arrange: Set up POST data and mocks
        $_POST['task_status'] = 'completed';
        $_POST['task_meta_nonce'] = 'valid_nonce';

        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'task_save_meta')
            ->andReturn(true);

        Monkey\Functions\expect('defined')
            ->once()
            ->with('DOING_AUTOSAVE')
            ->andReturn(false);

        Monkey\Functions\expect('current_user_can')
            ->once()
            ->with('edit_post', 123)
            ->andReturn(true);

        Monkey\Functions\expect('sanitize_text_field')
            ->once()
            ->with('completed')
            ->andReturn('completed');

        Monkey\Functions\expect('update_post_meta')
            ->once()
            ->with(123, '_task_status', 'completed');

        // Act: Call the method
        $meta_boxes = new Meta_Boxes();
        $meta_boxes->save_meta_data(123);

        // Assert: Test completed successfully
        $this->assertTrue(true);
    }

    public function test_save_meta_data_with_invalid_nonce() {
        // Arrange: Set up invalid nonce
        $_POST['task_meta_nonce'] = 'invalid_nonce';

        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('invalid_nonce', 'task_save_meta')
            ->andReturn(false);

        // update_post_meta should NOT be called
        Monkey\Functions\expect('update_post_meta')
            ->never();

        // Act: Call the method
        $meta_boxes = new Meta_Boxes();
        $meta_boxes->save_meta_data(123);

        // Assert: Test completed successfully
        $this->assertTrue(true);
    }
}
```

### Testing Admin Functionality

```php
<?php
namespace YourPlugin\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;
use YourPlugin\Admin;

class AdminTest extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_enqueue_scripts() {
        // Arrange: Mock WordPress functions
        Monkey\Functions\expect('wp_enqueue_script')
            ->once()
            ->with(
                'your-plugin-admin',
                \Mockery::type('string'),
                ['jquery'],
                \Mockery::type('string'),
                true
            );

        // Act: Call the method
        $admin = new Admin();
        $admin->enqueue_scripts();

        // Assert: Test completed successfully
        $this->assertTrue(true);
    }

    public function test_admin_menu_registration() {
        // Arrange: Mock menu functions
        Monkey\Functions\expect('add_menu_page')
            ->once()
            ->with(
                'Your Plugin',
                'Your Plugin',
                'manage_options',
                'your-plugin',
                \Mockery::type('array')
            );

        // Act: Call the method
        $admin = new Admin();
        $admin->add_admin_menu();

        // Assert: Test completed successfully
        $this->assertTrue(true);
    }
}
```

## Running Tests

### Basic Test Execution

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/unit/PostTypeTest.php

# Run specific test method
./vendor/bin/phpunit --filter test_register_post_type

# Run tests with coverage report
./vendor/bin/phpunit --coverage-html tests/coverage

# Run tests with verbose output
./vendor/bin/phpunit --verbose
```

### Creating a Test Runner Script

Create `run-tests.sh` for easier test execution:

```bash
#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}WordPress Plugin Test Runner${NC}"
echo "=================================="

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo -e "${RED}Error: Composer dependencies not installed${NC}"
    echo "Run: composer install"
    exit 1
fi

# Parse command line arguments
FILTER=""
COVERAGE=""
VERBOSE=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --filter)
            FILTER="--filter $2"
            shift 2
            ;;
        --coverage)
            COVERAGE="--coverage-html tests/coverage"
            shift
            ;;
        --verbose)
            VERBOSE="--verbose"
            shift
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Run tests
echo -e "${YELLOW}Running tests...${NC}"
./vendor/bin/phpunit $FILTER $COVERAGE $VERBOSE

# Check exit code
if [ $? -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
else
    echo -e "${RED}Some tests failed!${NC}"
    exit 1
fi
```

Make it executable:
```bash
chmod +x run-tests.sh
```

Usage examples:
```bash
# Run all tests
./run-tests.sh

# Run specific test
./run-tests.sh --filter test_register_post_type

# Run with coverage report
./run-tests.sh --coverage

# Run with verbose output
./run-tests.sh --verbose
```

## Best Practices

### 1. Test Naming Conventions
- Use descriptive test method names: `test_should_register_post_type_when_init_called`
- Group related tests in the same test class
- Use consistent naming patterns

### 2. Test Structure (AAA Pattern)
```php
public function test_example() {
    // Arrange: Set up test data and mocks
    $input = 'test data';
    Monkey\Functions\expect('wp_function')->once();
    
    // Act: Execute the code being tested
    $result = $object->method($input);
    
    // Assert: Verify the expected outcome
    $this->assertEquals('expected', $result);
}
```

### 3. Mock Appropriately
```php
// Mock WordPress functions
Monkey\Functions\expect('get_option')
    ->once()
    ->with('plugin_option')
    ->andReturn('default_value');

// Mock WordPress actions/filters
Monkey\Actions\expectAdded('init')
    ->once()
    ->with(\Mockery::type('callable'));
```

### 4. Test Edge Cases
```php
public function test_handles_empty_input() {
    $result = $this->object->process_data('');
    $this->assertEquals('default', $result);
}

public function test_handles_null_input() {
    $result = $this->object->process_data(null);
    $this->assertNull($result);
}
```

### 5. Use Data Providers for Multiple Test Cases
```php
/**
 * @dataProvider statusProvider
 */
public function test_status_validation($input, $expected) {
    $result = $this->validator->validate_status($input);
    $this->assertEquals($expected, $result);
}

public function statusProvider() {
    return [
        ['todo', true],
        ['in-progress', true],
        ['completed', true],
        ['invalid', false],
        ['', false],
    ];
}
```

## Understanding Test Results

### Test Statuses Explained

1. **Passed (.)**: Test executed successfully with all assertions passing
2. **Failed (F)**: Test executed but assertions failed
3. **Error (E)**: Test couldn't complete due to PHP errors
4. **Risky (R)**: Test has no assertions or other quality issues
5. **Skipped (S)**: Test was intentionally skipped
6. **Incomplete (I)**: Test was marked as incomplete

### Sample Output Analysis
```
Tests: 6, Assertions: 9, Errors: 1, Risky: 4.
```
This means:
- 6 test methods were executed
- 9 assertion checks were performed
- 1 test had an error (couldn't complete)
- 4 tests were marked as risky (likely no assertions)

### Improving Test Quality
```php
// Risky test (no assertions)
public function test_init() {
    $object = new MyClass();
    $object->init();
}

// Improved test (with assertions)
public function test_init() {
    Monkey\Actions\expectAdded('init')->once();
    
    $object = new MyClass();
    $object->init();
    
    $this->assertTrue(true); // At minimum
}
```

## Troubleshooting Common Issues

### 1. "Class not found" Errors
```bash
# Solution: Regenerate autoloader
composer dump-autoload
```

### 2. WordPress Function Undefined
```php
// Add to bootstrap.php
if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script() {
        // Mock implementation
    }
}
```

### 3. PHPUnit Command Not Found
```bash
# Check if vendor directory exists
ls -la vendor/bin/

# If missing, reinstall dependencies
rm -rf vendor
composer install
```

### 4. Brain\Monkey Issues
```php
// Ensure proper setup/teardown
protected function setUp(): void {
    parent::setUp();
    Brain\Monkey\setUp();
}

protected function tearDown(): void {
    Brain\Monkey\tearDown();
    parent::tearDown();
}
```

## Continuous Integration Setup

### GitHub Actions Example (`.github/workflows/tests.yml`)
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php-versions: ['7.4', '8.0', '8.1']
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-versions }}
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Run tests
      run: composer test
```

## Conclusion

Testing WordPress plugins might seem complex initially, but it's an investment that pays dividends in code quality, maintainability, and developer confidence. Start with simple unit tests and gradually add more complex integration tests as you become comfortable with the testing workflow.

Key takeaways:
1. Set up proper tooling (PHPUnit, Brain\Monkey, Composer)
2. Follow consistent test structure and naming
3. Mock WordPress functions appropriately
4. Write meaningful assertions
5. Test both success and failure scenarios
6. Use continuous integration for automated testing

Remember: Good tests are not just about coverage percentage—they're about testing the right things in the right way to ensure your plugin works reliably for your users.

## Additional Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Brain\Monkey Documentation](https://brain-wp.github.io/BrainMonkey/)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
