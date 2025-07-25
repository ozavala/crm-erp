# Multi-Company Testing Guide

This guide explains how to run the tests that verify the multi-company implementation works correctly.

## Overview

We've created a comprehensive suite of tests to verify that the multi-company implementation works correctly across all aspects of the application. These tests cover:

- Data isolation between companies
- User-company associations
- Access control based on company membership
- Super admin capabilities
- Cross-company operations
- Google Calendar integration
- And more...

## Prerequisites

Before running the tests, ensure you have:

1. A local development environment set up
2. The latest code pulled from the repository
3. Dependencies installed via Composer
4. A test database configured (SQLite is recommended for testing)

## Running the Tests

### Setup

1. Configure your `.env.testing` file:

```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

2. Make sure your `phpunit.xml` file is properly configured:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="TELESCOPE_ENABLED" value="false"/>
</php>
```

### Running All Multi-Company Tests

To run all multi-company tests:

```bash
php artisan test --filter=MultiCompany
```

This will run all tests with "MultiCompany" in their name.

### Running Specific Tests

You can run specific test files to focus on particular aspects of the multi-company implementation:

```bash
# Test user-company associations
php artisan test tests/Unit/Models/CrmUserTest.php

# Test basic multi-company data isolation
php artisan test tests/Feature/MultiCompanyTest.php

# Test the entire multi-company structure
php artisan test tests/Feature/MultiCompanyIntegrationTest.php

# Test Google Calendar integration in multi-company context
php artisan test tests/Feature/GoogleCalendarMultiCompanyTest.php

# Test calendar functionality in multi-company context
php artisan test tests/Feature/CalendarMultiCompanyTest.php

# Test accounting functionality in multi-company context
php artisan test tests/Feature/AccountingMultiCompanyTest.php

# Test CRM functionality in multi-company context
php artisan test tests/Feature/CrmMultiCompanyTest.php

# Test inventory functionality in multi-company context
php artisan test tests/Feature/InventoryMultiCompanyTest.php

# Test user management in multi-company context
php artisan test tests/Feature/UserManagementMultiCompanyTest.php

# Test API functionality in multi-company context
php artisan test tests/Feature/ApiMultiCompanyTest.php

# Test reporting functionality in multi-company context
php artisan test tests/Feature/ReportingMultiCompanyTest.php

# Test notification functionality in multi-company context
php artisan test tests/Feature/NotificationMultiCompanyTest.php
```

### Running Individual Test Methods

You can also run specific test methods to focus on particular aspects:

```bash
php artisan test --filter=MultiCompanyTest::multi_company_structure_maintains_data_isolation
```

## Test Coverage

Here's what each test file covers:

### Unit Tests

#### `CrmUserTest`

- Verifies that users are correctly associated with companies
- Tests user-company relationships
- Verifies that users can only access their company's data

### Feature Tests

#### `MultiCompanyTest`

- Basic multi-company data isolation
- Verifies that users can only see data from their own company
- Tests that super admins can see data from all companies

#### `MultiCompanyIntegrationTest`

- End-to-end verification of the multi-company structure
- Tests data isolation across multiple entities
- Verifies relationship integrity between entities
- Tests cross-company operations for super admins

#### `GoogleCalendarMultiCompanyTest`

- Tests Google Calendar integration in multi-company context
- Verifies that calendar settings are isolated between companies
- Tests that calendar events are properly associated with companies
- Verifies that Google Calendar synchronization respects company boundaries

#### `CalendarMultiCompanyTest`

- Tests calendar functionality in multi-company context
- Verifies that appointments, tasks, and calendar events are isolated between companies
- Tests that users can only access calendar data from their own company

#### `AccountingMultiCompanyTest`

- Tests accounting functionality in multi-company context
- Verifies that accounts, journal entries, and financial reports are isolated between companies
- Tests that tax reports are properly scoped to companies

#### `CrmMultiCompanyTest`

- Tests CRM functionality in multi-company context
- Verifies that leads, opportunities, and quotations are isolated between companies
- Tests lead-to-customer conversion in multi-company context

#### `InventoryMultiCompanyTest`

- Tests inventory functionality in multi-company context
- Verifies that products, warehouses, and inventory movements are isolated between companies
- Tests that stock levels are properly scoped to companies

#### `UserManagementMultiCompanyTest`

- Tests user management in multi-company context
- Verifies that company admins can only manage users in their own company
- Tests role and permission assignment in multi-company context
- Verifies company switching for super admins

#### `ApiMultiCompanyTest`

- Tests API functionality in multi-company context
- Verifies that API tokens are scoped to companies
- Tests that API requests respect company boundaries
- Verifies that super admin API tokens can access all companies

#### `ReportingMultiCompanyTest`

- Tests reporting functionality in multi-company context
- Verifies that reports only include data from the user's company
- Tests that report exports respect company boundaries
- Verifies that super admins can see data from all companies in reports

#### `NotificationMultiCompanyTest`

- Tests notification functionality in multi-company context
- Verifies that notifications are isolated between companies
- Tests that users only receive notifications relevant to their company
- Verifies notification creation and management in multi-company context

## Troubleshooting

### Common Issues

1. **Database Connection Issues**

   If you encounter database connection issues, make sure your `.env.testing` file is properly configured and that you have the necessary permissions to create and modify the test database.

2. **Missing Dependencies**

   If you encounter missing dependencies, run `composer install` to ensure all required packages are installed.

3. **Failed Assertions**

   If tests fail with assertion errors, check the error messages carefully. They often provide valuable information about what went wrong. Common issues include:
   
   - Missing or incorrect owner_company_id values
   - Global scopes not properly applied
   - Permission issues
   - Incorrect relationships

4. **Memory Issues**

   If you encounter memory issues when running all tests, try running them individually or increase the memory limit in your PHP configuration.

### Getting Help

If you encounter issues that you can't resolve, please:

1. Check the documentation in the `docs` directory
2. Review the test files to understand what they're testing
3. Consult with the development team

## Extending the Tests

When adding new features to the application, consider adding tests to verify that they work correctly in a multi-company context. Here are some guidelines:

1. **Follow the existing patterns**: Look at the existing tests for examples of how to test multi-company functionality.
2. **Test both regular users and super admins**: Make sure to test both regular users (who should only see their company's data) and super admins (who should see all data).
3. **Test data isolation**: Verify that data is properly isolated between companies.
4. **Test cross-company operations**: For super admins, test that they can perform operations across companies.
5. **Test edge cases**: Consider edge cases like what happens when a user tries to access data from another company.

## Conclusion

Running these tests regularly will help ensure that the multi-company implementation continues to work correctly as the application evolves. If you encounter any issues or have questions about the tests, please consult with the development team.