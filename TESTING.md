# Testing Guide

## Overview

This plugin includes comprehensive test coverage for all functionality including:

- Unit tests for individual classes and methods
- Integration tests for AJAX functionality  
- JavaScript tests for frontend interactions
- Security and authentication testing

## Running Tests

### PHP Tests

Run all tests:
```bash
./vendor/bin/phpunit
```

Run only unit tests:
```bash
./vendor/bin/phpunit --testsuite="Tasks Manager Unit Tests"
```

Run only integration tests:
```bash
./vendor/bin/phpunit --testsuite="Tasks Manager Integration Tests"
```

Run with coverage:
```bash
./vendor/bin/phpunit --coverage-html coverage/
```

### JavaScript Tests

JavaScript tests are located in `tests/js/` and can be run with any JavaScript testing framework like Jest or Jasmine.

## Test Structure

### Unit Tests (`tests/unit/`)

- `TasksPostTypeTest.php` - Tests post type registration
- `TasksTaxonomyTest.php` - Tests taxonomy registration  
- `TasksPublicTest.php` - Tests public-facing functionality
- `TasksMetaBoxesTest.php` - Tests admin meta boxes

### Integration Tests (`tests/integration/`)

- `TasksAjaxIntegrationTest.php` - Tests complete AJAX workflows
- Tests user authentication and security
- Tests complete task and subtask lifecycle

### JavaScript Tests (`tests/js/`)

- `tasks-frontend.test.js` - Tests jQuery functionality
- Tests form submissions and AJAX calls
- Tests UI interactions and error handling

## Test Features Covered

### Security Testing
- ✅ CSRF protection via nonces
- ✅ User authentication checks
- ✅ Input sanitization
- ✅ Authorization validation

### AJAX Functionality
- ✅ Add new tasks
- ✅ Add subtasks to existing tasks
- ✅ Update task status
- ✅ Update subtask status
- ✅ Error handling and validation

### User Interface
- ✅ Form validation
- ✅ Dynamic content updates
- ✅ Status indicators
- ✅ Responsive design
- ✅ Accessibility features

### User Access Control
- ✅ Login requirement enforcement
- ✅ User-specific task display
- ✅ Author-based task filtering

## Test Data

The tests use mocked WordPress functions and dummy data. No real database interactions occur during testing.

## Continuous Integration

Tests can be integrated with CI/CD pipelines using the following commands:

```bash
# Install dependencies
composer install --no-dev

# Run tests
./vendor/bin/phpunit --configuration phpunit.xml

# Generate coverage reports
./vendor/bin/phpunit --coverage-clover coverage.xml
```

## Manual Testing Checklist

### Basic Functionality
- [ ] Plugin activates without errors
- [ ] Tasks can be created via frontend form
- [ ] Subtasks can be added to existing tasks
- [ ] Task status can be updated
- [ ] Subtask status can be updated
- [ ] Only logged-in users can access task manager

### UI/UX Testing
- [ ] Dark theme displays correctly
- [ ] Forms are responsive on mobile devices
- [ ] Error messages are user-friendly
- [ ] Loading states provide feedback
- [ ] Form validation works properly

### Security Testing
- [ ] Non-logged-in users see login prompt
- [ ] AJAX requests include proper nonces
- [ ] Users can only see their own tasks
- [ ] SQL injection attempts are blocked
- [ ] XSS attempts are sanitized

### Performance Testing
- [ ] Page loads quickly with many tasks
- [ ] AJAX requests complete in reasonable time
- [ ] No JavaScript errors in console
- [ ] Memory usage is reasonable
