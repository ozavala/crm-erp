# Multi-Company Implementation Documentation

## Overview

This document outlines the implementation of multi-tenancy in the CRM/ERP system using the owner_company_id approach. The system now supports multiple companies operating within the same application instance, with proper data isolation and access control.

## Architecture

### Data Isolation

The multi-company architecture is implemented using a **discriminator column** approach:

- Each entity in the system has an `owner_company_id` column that associates it with a specific company
- Database queries are automatically scoped to the current user's company using global scopes
- Super admins can access data across all companies

### User Roles and Permissions

Three main user types exist in the system:

1. **Regular Users**: Can only access data within their own company
2. **Company Admins**: Can manage users and data within their own company
3. **Super Admins**: Can manage all companies, users, and data across the entire system

### Company Switching

Super admins have the ability to switch between companies to view and manage data in the context of any company. This is implemented through a company selector in the UI and session-based company context.

## Implementation Details

### Database Schema

All major entities in the system have been updated to include the `owner_company_id` foreign key:

- Customers
- Products
- Suppliers
- Invoices
- Orders
- Payments
- Leads
- Opportunities
- Quotations
- Appointments
- Tasks
- Calendar Events
- Calendar Settings
- Journal Entries
- Accounts
- Notifications
- API Tokens
- And more...

### Global Scopes

A global scope is applied to all multi-company models to automatically filter queries based on the current user's company:

```php
protected static function booted()
{
    parent::booted();
    
    static::addGlobalScope('company', function (Builder $builder) {
        if (auth()->check() && !auth()->user()->is_super_admin) {
            $builder->where('owner_company_id', auth()->user()->owner_company_id);
        }
    });
}
```

### Middleware

A middleware is used to ensure users can only access routes and data for their own company:

```php
public function handle($request, Closure $next)
{
    $user = auth()->user();
    
    if (!$user) {
        return redirect('login');
    }
    
    // Set the current company in the session
    if (!session()->has('current_company_id')) {
        session(['current_company_id' => $user->owner_company_id]);
    }
    
    // If user is trying to switch companies
    if ($request->has('company') && $user->is_super_admin) {
        session(['current_company_id' => $request->company]);
    } elseif ($request->has('company') && !$user->is_super_admin) {
        // Regular users can't switch companies
        session(['current_company_id' => $user->owner_company_id]);
    }
    
    return $next($request);
}
```

### Controllers

Controllers have been updated to ensure data is properly scoped to the current company:

```php
public function index()
{
    $customers = Customer::all(); // Global scope automatically applies company filter
    return view('customers.index', compact('customers'));
}

public function store(StoreCustomerRequest $request)
{
    $validated = $request->validated();
    
    // For regular users, always set owner_company_id to their company
    if (!auth()->user()->is_super_admin) {
        $validated['owner_company_id'] = auth()->user()->owner_company_id;
    }
    
    $customer = Customer::create($validated);
    return redirect()->route('customers.show', $customer);
}
```

### API Authentication

API tokens are scoped to specific companies:

- Each API token belongs to a specific company
- API requests are authenticated and scoped to the company associated with the token
- Super admin tokens can access data across all companies

## Google Calendar Integration

The Google Calendar integration has been updated to support multiple companies:

- Each company can have its own Google Calendar settings
- Calendar synchronization is scoped to the user's company
- Events are properly associated with the correct company

## Testing

Comprehensive tests have been created to verify the multi-company implementation:

1. **Unit Tests**:
   - `CrmUserTest`: Verifies user-company associations

2. **Feature Tests**:
   - `MultiCompanyTest`: Basic multi-company data isolation
   - `MultiCompanyIntegrationTest`: End-to-end verification of the multi-company structure
   - `GoogleCalendarMultiCompanyTest`: Google Calendar integration in multi-company context
   - `CalendarMultiCompanyTest`: Calendar functionality in multi-company context
   - `AccountingMultiCompanyTest`: Accounting functionality in multi-company context
   - `CrmMultiCompanyTest`: CRM functionality in multi-company context
   - `InventoryMultiCompanyTest`: Inventory functionality in multi-company context
   - `UserManagementMultiCompanyTest`: User management in multi-company context
   - `ApiMultiCompanyTest`: API functionality in multi-company context
   - `ReportingMultiCompanyTest`: Reporting functionality in multi-company context
   - `NotificationMultiCompanyTest`: Notification functionality in multi-company context

### Test Coverage

The tests verify:

- Data isolation between companies
- Proper user-company associations
- Access control based on company membership
- Super admin ability to access all companies
- Cross-company operations for super admins
- Prevention of cross-company operations for regular users
- Google Calendar integration in multi-company context
- Reporting and data aggregation respecting company boundaries
- API token scoping to companies
- Notification isolation between companies

## Best Practices

When developing new features or modifying existing ones, follow these guidelines:

1. **Always include owner_company_id**:
   - Add the column to any new entities
   - Include it in migrations, models, and factories
   - Set up the proper relationships

2. **Use global scopes**:
   - Ensure new models use the company global scope
   - Test that queries are properly scoped

3. **Validate company access**:
   - In controllers, validate that users can only access their company's data
   - For super admins, allow cross-company access

4. **Test thoroughly**:
   - Create tests that verify data isolation
   - Test both regular user and super admin scenarios

## Deployment Considerations

When deploying the multi-company implementation:

1. **Database Migration**:
   - Run all migrations to add owner_company_id columns
   - Ensure existing data is properly associated with companies

2. **User Assignment**:
   - Assign existing users to appropriate companies
   - Set up super admin users

3. **Data Verification**:
   - Verify that data is properly isolated between companies
   - Check that reports and dashboards show correct company-specific data

## Future Enhancements

Potential future enhancements to the multi-company implementation:

1. **Company-specific configurations**:
   - Allow different settings per company
   - Support company-specific workflows

2. **Cross-company reporting**:
   - Enhanced reporting capabilities for super admins
   - Comparative analysis between companies

3. **Company groups**:
   - Support for grouping related companies
   - Shared data between company groups

4. **White-labeling**:
   - Company-specific branding
   - Custom domains per company