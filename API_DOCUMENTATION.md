# API Documentation - CRM/ERP System

## Base URL
```
http://your-domain.com/api
```

## Authentication
La API utiliza Laravel Sanctum para autenticación. Todos los endpoints protegidos requieren un token Bearer en el header `Authorization`.

### Headers requeridos
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Autenticación

#### Login
```http
POST /api/auth/login
```

**Body:**
```json
{
    "email": "user@example.com",
    "password": "password",
    "device_name": "iPhone 12" // opcional
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

#### Register
```http
POST /api/auth/register
```

**Body:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "device_name": "iPhone 12" // opcional
}
```

#### Get User
```http
GET /api/auth/user
```

#### Logout
```http
POST /api/auth/logout
```

#### Refresh Token
```http
POST /api/auth/refresh
```

#### Change Password
```http
POST /api/auth/change-password
```

**Body:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

### Customers

#### List Customers
```http
GET /api/customers
```

**Query Parameters:**
- `search` - Buscar por nombre, email o teléfono
- `status` - Filtrar por estado (active, inactive, prospect)
- `sort_by` - Campo para ordenar (default: created_at)
- `sort_order` - Orden (asc, desc)
- `per_page` - Elementos por página (default: 15)

#### Get Customer
```http
GET /api/customers/{id}
```

#### Create Customer
```http
POST /api/customers
```

**Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "tax_number": "123456789",
    "status": "active",
    "notes": "Cliente importante"
}
```

#### Update Customer
```http
PUT /api/customers/{id}
```

#### Delete Customer
```http
DELETE /api/customers/{id}
```

### Products

#### List Products
```http
GET /api/products
```

**Query Parameters:**
- `search` - Buscar por nombre, SKU o descripción
- `category_id` - Filtrar por categoría
- `status` - Filtrar por estado
- `low_stock` - Solo productos con stock bajo

#### Get Product
```http
GET /api/products/{id}
```

#### Create Product
```http
POST /api/products
```

**Body:**
```json
{
    "name": "Product Name",
    "sku": "PROD-001",
    "description": "Product description",
    "price": 99.99,
    "cost_price": 50.00,
    "category_id": 1,
    "status": "active",
    "tax_rate_id": 1,
    "weight": 1.5,
    "dimensions": "10x5x2",
    "reorder_point": 10,
    "max_stock": 100
}
```

#### Update Product
```http
PUT /api/products/{id}
```

#### Delete Product
```http
DELETE /api/products/{id}
```

#### Get Product Stock
```http
GET /api/products/{id}/stock
```

#### Update Product Stock
```http
POST /api/products/{id}/stock
```

**Body:**
```json
{
    "warehouse_id": 1,
    "quantity": 50,
    "operation": "add" // add, subtract, set
}
```

### Orders

#### List Orders
```http
GET /api/orders
```

**Query Parameters:**
- `search` - Buscar por número de orden o cliente
- `status` - Filtrar por estado
- `customer_id` - Filtrar por cliente
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

#### Get Order
```http
GET /api/orders/{id}
```

#### Create Order
```http
POST /api/orders
```

**Body:**
```json
{
    "customer_id": 1,
    "order_date": "2024-01-15",
    "due_date": "2024-01-30",
    "status": "pending",
    "notes": "Order notes",
    "shipping_address": "123 Main St",
    "billing_address": "123 Main St",
    "shipping_cost": 10.00,
    "discount_amount": 5.00,
    "tax_amount": 15.00,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "unit_price": 50.00,
            "discount": 0
        }
    ]
}
```

#### Update Order
```http
PUT /api/orders/{id}
```

#### Delete Order
```http
DELETE /api/orders/{id}
```

#### Update Order Status
```http
POST /api/orders/{id}/status
```

**Body:**
```json
{
    "status": "confirmed",
    "notes": "Order confirmed"
}
```

### Invoices

#### List Invoices
```http
GET /api/invoices
```

**Query Parameters:**
- `search` - Buscar por número de factura o cliente
- `status` - Filtrar por estado
- `customer_id` - Filtrar por cliente
- `date_from` - Fecha desde
- `date_to` - Fecha hasta
- `overdue` - Solo facturas vencidas

#### Get Invoice
```http
GET /api/invoices/{id}
```

#### Create Invoice
```http
POST /api/invoices
```

**Body:**
```json
{
    "customer_id": 1,
    "order_id": 1,
    "invoice_date": "2024-01-15",
    "due_date": "2024-01-30",
    "status": "draft",
    "notes": "Invoice notes",
    "billing_address": "123 Main St",
    "shipping_cost": 10.00,
    "discount_amount": 5.00,
    "tax_amount": 15.00,
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "unit_price": 50.00,
            "discount": 0
        }
    ]
}
```

#### Update Invoice
```http
PUT /api/invoices/{id}
```

#### Delete Invoice
```http
DELETE /api/invoices/{id}
```

#### Send Invoice
```http
POST /api/invoices/{id}/send
```

**Body:**
```json
{
    "send_email": true,
    "email_template": "custom"
}
```

### Purchase Orders

#### List Purchase Orders
```http
GET /api/purchase-orders
```

**Query Parameters:**
- `search` - Buscar por número de PO o proveedor
- `status` - Filtrar por estado
- `supplier_id` - Filtrar por proveedor
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

#### Get Purchase Order
```http
GET /api/purchase-orders/{id}
```

#### Create Purchase Order
```http
POST /api/purchase-orders
```

**Body:**
```json
{
    "supplier_id": 1,
    "order_date": "2024-01-15",
    "expected_delivery_date": "2024-01-30",
    "status": "draft",
    "notes": "PO notes",
    "shipping_address": "123 Main St",
    "billing_address": "123 Main St",
    "shipping_cost": 10.00,
    "discount_amount": 5.00,
    "tax_amount": 15.00,
    "items": [
        {
            "product_id": 1,
            "quantity": 10,
            "unit_price": 25.00,
            "discount": 0
        }
    ]
}
```

#### Update Purchase Order
```http
PUT /api/purchase-orders/{id}
```

#### Delete Purchase Order
```http
DELETE /api/purchase-orders/{id}
```

#### Update Purchase Order Status
```http
POST /api/purchase-orders/{id}/status
```

#### Receive Stock
```http
POST /api/purchase-orders/{id}/receive
```

**Body:**
```json
{
    "receipt_date": "2024-01-20",
    "warehouse_id": 1,
    "notes": "Stock received",
    "items": [
        {
            "product_id": 1,
            "quantity_received": 10,
            "unit_cost": 25.00
        }
    ]
}
```

### Reports

#### Sales Report
```http
GET /api/reports/sales
```

**Query Parameters:**
- `date_from` - Fecha desde
- `date_to` - Fecha hasta
- `group_by` - Agrupar por (day, week, month, year)

#### Inventory Report
```http
GET /api/reports/inventory
```

**Query Parameters:**
- `low_stock` - Solo productos con stock bajo
- `category_id` - Filtrar por categoría

#### Cash Flow Report
```http
GET /api/reports/cash-flow
```

**Query Parameters:**
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

#### Profitability Report
```http
GET /api/reports/profitability
```

**Query Parameters:**
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

#### Supplier Performance Report
```http
GET /api/reports/supplier-performance
```

**Query Parameters:**
- `date_from` - Fecha desde
- `date_to` - Fecha hasta

### Dashboard

#### Dashboard Stats
```http
GET /api/dashboard/stats
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_customers": 150,
        "total_products": 500,
        "pending_orders": 25,
        "pending_invoices": 30,
        "low_stock_products": 15,
        "monthly_sales": 50000.00
    }
}
```

### Public Endpoints

#### Health Check
```http
GET /api/health
```

#### Version Info
```http
GET /api/version
```

## Códigos de Respuesta

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `422` - Validation Error
- `500` - Server Error

## Ejemplos de Uso

### JavaScript/Fetch
```javascript
// Login
const loginResponse = await fetch('/api/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password'
    })
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Get customers with token
const customersResponse = await fetch('/api/customers', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
});

const customers = await customersResponse.json();
```

### cURL
```bash
# Login
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get customers
curl -X GET http://your-domain.com/api/customers \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### PHP
```php
// Login
$response = Http::post('/api/auth/login', [
    'email' => 'user@example.com',
    'password' => 'password'
]);

$token = $response->json('data.token');

// Get customers
$customers = Http::withToken($token)
    ->get('/api/customers')
    ->json();
```

## Notas Importantes

1. **Autenticación**: Todos los endpoints (excepto auth y públicos) requieren autenticación con token Bearer.

2. **Validación**: Todos los endpoints incluyen validación de datos con mensajes de error detallados.

3. **Paginación**: Los endpoints de listado incluyen paginación automática.

4. **Filtros**: La mayoría de endpoints de listado incluyen filtros opcionales.

5. **Transacciones**: Las operaciones complejas (crear órdenes, facturas, etc.) utilizan transacciones de base de datos.

6. **Relaciones**: Los endpoints de detalle incluyen relaciones cargadas automáticamente.

7. **Estados**: Los modelos tienen estados predefinidos que controlan el flujo de trabajo.

8. **Stock**: Las operaciones de stock incluyen validaciones para evitar cantidades negativas.

9. **Reportes**: Los reportes incluyen parámetros de fecha y agrupación flexibles.

10. **Seguridad**: Las operaciones de eliminación incluyen validaciones para evitar eliminar registros con relaciones. 