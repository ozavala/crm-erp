<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
       
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                {{ __('Dashboard') }}
            </a>
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            {{ __('Dashboard') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Home') }}</a>
                </li>
                @can('view-admin-section')
                <li class="nav-item dropdown">
                    <a id="navbarDropdownAdmin" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Admin') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAdmin">
                        @can('view-settings')
                        <a class="dropdown-item" href="{{ route('settings.edit') }}">
                            {{ __('Settings') }}
                        </a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('tax-settings.index') }}">
                            {{ __('Tax Settings') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('crm-users.index') }}">
                            {{ __('CRM Users') }}
                        </a>
                        @can('view-roles')
                        <a class="dropdown-item" href="{{ route('user-roles.index') }}">
                            {{ __('User Roles') }}
                        </a>
                        @endcan
                        @can('view-permissions')
                        <a class="dropdown-item" href="{{ route('permissions.index') }}">
                            {{ __('Permissions') }}
                        </a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('addresses.index') }}">
                            {{ __('Addresses') }}
                        </a>
                    </div>
                </li>
                @endcan
                <li class="nav-item dropdown">
                    <a id="navbarDropdownSales" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Sales') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownSales">
                        <a class="dropdown-item" href="{{ route('customers.index') }}">
                            {{ __('Customers') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('contacts.index') }}">
                            {{ __('Contacts') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('leads.index') }}">
                            {{ __('Leads') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('opportunities.index') }}">
                            {{ __('Opportunities') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('quotations.index') }}">
                            {{ __('Quotations') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('orders.index') }}">
                            {{ __('Sales Orders') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('invoices.index') }}">
                            {{ __('Invoices') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownPurchasing" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Purchasing') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownPurchasing">
                        <a class="dropdown-item" href="{{ route('suppliers.index') }}">
                            {{ __('Suppliers') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('purchase-orders.index') }}">
                            {{ __('Purchase Orders') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('bills.index') }}">
                            {{ __('Bills') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownInventory" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Inventory') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownInventory">
                        <a class="dropdown-item" href="{{ route('products.index') }}">
                            {{ __('Products/Services') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('product-categories.index') }}">
                            {{ __('Product Categories') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('product-features.index') }}">
                            {{ __('Product Features') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('warehouses.index') }}">
                            {{ __('Warehouses') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownAccounting" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Accounting') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownAccounting">
                        <a class="dropdown-item" href="{{ route('payments.index') }}">
                            {{ __('Payments') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('journal-entries.index') }}">
                            {{ __('Journal Entries') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownReports" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('Reports') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownReports">
                        <a class="dropdown-item" href="{{ route('reports.sales') }}">
                            {{ __('Sales by Period') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('reports.sales-by-product') }}">
                            {{ __('Sales by Product') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('reports.sales-by-customer') }}">
                            {{ __('Sales by Customer') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('reports.sales-by-employee') }}">
                            {{ __('Sales by Employee') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('reports.sales-by-category') }}">
                            {{ __('Sales by Category') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('reports.profit_and_loss') }}">
                            {{ __('Profit and Loss') }}
                        </a>
                    </div>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                        {{ Auth::user()->full_name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            {{ __('Profile') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                            {{ __('Log Out') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>