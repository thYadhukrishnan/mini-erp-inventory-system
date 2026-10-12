# Mini ERP — Inventory & Purchase Order System

A lightweight, production-grade Mini ERP Inventory and Purchase Order Management System built with **Laravel 12**, **MySQL**, **Bootstrap 5**, and **Laravel Sanctum**.

---

## 1. Setup Commands

```bash
# 1. Clone repository and navigate to project directory
cd mini-erp

# 2. Install dependencies
composer install

# 3. Setup environment file
cp .env.example .env
# Configure DB_DATABASE=mini_erp, DB_USERNAME=root, DB_PASSWORD=

# 4. Generate application encryption key
php artisan key:generate

# 5. Run database migrations and seed test data
php artisan migrate --seed

# 6. Start the local server
php artisan serve
```

> **Web Application:** [http://localhost:8000](http://localhost:8000)

---

## 2. Test Execution Instructions

The project includes unit and feature test suites.

```bash
# Run all tests using Artisan
php artisan test

# Or run directly via PHPUnit
./vendor/bin/phpunit
```

All 23 tests validate line item calculations, domain stock exceptions, state transitions with atomic stock updates, and API security/validation.

---

## 3. Default Login Credentials

Populated automatically by `DatabaseSeeder`:

- **Web Dashboard:** `http://localhost:8000/login`
- **API Endpoint:** `POST http://localhost:8000/api/login`
- **Email:** `admin@example.com`
- **Password:** `password`

*The seeder also generates 3 suppliers and 10 inventory items with realistic stock levels and thresholds.*

---

## 4. Indexing & Optimization Decisions

- **Composite Index on `products (stock, low_stock_threshold)`**:
  Accelerates low-stock inventory filtering (`WHERE stock < low_stock_threshold`) without table scans.
- **Composite Index on `purchase_orders (supplier_id, status)`**:
  Optimizes queries filtering orders by supplier and status during reports and dashboard aggregation.
- **Foreign Key Index on `purchase_order_items (purchase_order_id, product_id)`**:
  Speeds up relationship queries and line item lookups.
- **Atomic Stock Mutations & Race Condition Prevention**:
  Stock count increments only on `RECEIVED` status, wrapped in `DB::transaction()` with pessimistic row locking (`Product::lockForUpdate()`).

---

## 5. API Testing (cURL Commands)

All API endpoints require the `Accept: application/json` header. Protected endpoints require `Authorization: Bearer <TOKEN>`.

### 1. Login (Retrieve Token)
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 2. View Inventory
```bash
curl -X GET http://127.0.0.1:8000/api/inventory \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

### 3. View Low Stock Items
```bash
curl -X GET http://127.0.0.1:8000/api/inventory/low-stock \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

### 4. Create Purchase Order
```bash
curl -X POST http://127.0.0.1:8000/api/purchase-orders \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{
    "supplier_id": 1,
    "items": [
      {"product_id": 1, "quantity": 5, "unit_price": 1200.00},
      {"product_id": 2, "quantity": 10, "unit_price": 75.00}
    ]
  }'
```

### 5. Transition PO Status
**Approve Order:**
```bash
curl -X PATCH http://127.0.0.1:8000/api/purchase-orders/1/status \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{"status":"APPROVED"}'
```

**Receive Order (Atomically Increments Stock):**
```bash
curl -X PATCH http://127.0.0.1:8000/api/purchase-orders/1/status \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d '{"status":"RECEIVED"}'
```

### 6. Supplier Spend Report
```bash
curl -X GET http://127.0.0.1:8000/api/reports/supplier-spend \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>"
```

---

## 6. Postman Collection

An exported Postman collection is included in the project:
- Import [`mini_erp_postman_collection.json`](mini_erp_postman_collection.json) into Postman.
- The `Login` request automatically sets the Bearer token into the `{{token}}` collection variable for all remaining requests.