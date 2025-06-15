<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
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
               
                
                <li class="nav-item dropdown">
                    <a id="navbarDropdownProducts" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Users') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUsers">
                        <a class="dropdown-item" href="{{ route('crm-users.index') }}">
                            {{ __('Crm Users') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('user-roles.index') }}">
                            {{ __('User Roles') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('addresses.index') }}">
                            {{ __('Addresses') }}
                        </a>

                        <a class="dropdown-item" href="{{ route('permissions.index') }}">
                            {{ __('Pemissions') }}
                        </a>
                        
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="navbarDropdownProducts" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Customers') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUsers">
                        <a class="dropdown-item" href="{{ route('customers.index') }}">
                            {{ __('Customers') }}
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
                    <a id="navbarDropdownProducts" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Products') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownProducts">
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
                    <a id="navbarDropdownProducts" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Suppliers') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUsers">
                        <a class="dropdown-item" href="{{ route('suppliers.index') }}">
                            {{ __('Suppliers') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('purchase-orders.index') }}">
                            {{ __('Purchase Orders') }}
                        </a>
                     
                    </div>
                </li>
                
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
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