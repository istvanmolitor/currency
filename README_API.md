# Currency Package - Backend API

Laravel backend package for currency management with full CRUD API.

## Features

- Complete REST API for currency management
- Create, read, update, and delete currencies
- Pagination, search, and sorting support
- Default currency protection (cannot be deleted)
- Validation with Form Requests
- API Resources for consistent data formatting
- Full OpenAPI documentation support

## Structure

```
currency/
├── src/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── CurrencyController.php    # Main CRUD controller
│   │   ├── Requests/
│   │   │   ├── StoreCurrencyRequest.php  # Validation for create
│   │   │   └── UpdateCurrencyRequest.php # Validation for update
│   │   └── Resources/
│   │       └── CurrencyResource.php       # API resource transformer
│   ├── Models/
│   │   └── Currency.php                   # Currency Eloquent model
│   ├── Providers/
│   │   └── CurrencyServiceProvider.php    # Service provider
│   └── routes/
│       └── api.php                        # API route definitions
└── resources/
    └── lang/
        ├── en/
        │   └── currency.php               # English translations
        └── hu/
            └── currency.php               # Hungarian translations
```

## API Endpoints

All endpoints are prefixed with `/api/admin/currency` and require authentication (`auth:sanctum` middleware).

### List Currencies
```
GET /api/admin/currency/currencies
```
Query parameters:
- `search` - Search in code and name fields
- `sort` - Sort by field
- `direction` - Sort direction (asc/desc)
- `page` - Page number

Response:
```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 5
  },
  "filters": {
    "search": "",
    "sort": "code",
    "direction": "asc"
  }
}
```

### Get Create Form Data
```
GET /api/admin/currency/currencies/create
```

### Create Currency
```
POST /api/admin/currency/currencies
```
Body:
```json
{
  "code": "USD",
  "name": "US Dollar",
  "symbol": "$",
  "decimals": 2,
  "decimal_separator": ".",
  "thousands_separator": ",",
  "is_symbol_first": true,
  "is_enabled": true,
  "is_default": false
}
```

### Show Currency
```
GET /api/admin/currency/currencies/{id}
```

### Get Edit Form Data
```
GET /api/admin/currency/currencies/{id}/edit
```

### Update Currency
```
PUT /api/admin/currency/currencies/{id}
```
Body: Same as create

### Delete Currency
```
DELETE /api/admin/currency/currencies/{id}
```
Note: Cannot delete default currency.

## Currency Model Fields

- `id` - Primary key
- `code` - Currency code (3 characters, unique)
- `name` - Currency name
- `symbol` - Currency symbol
- `decimals` - Number of decimal places (0-8)
- `decimal_separator` - Decimal separator character
- `thousands_separator` - Thousands separator character
- `is_symbol_first` - Whether symbol appears before amount
- `is_enabled` - Whether currency is enabled
- `is_default` - Whether this is the default currency
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Validation Rules

### Create Currency
- `code`: required, string, max 3 characters, unique
- `name`: required, string, max 255 characters
- `symbol`: required, string, max 10 characters
- `decimals`: optional, integer, 0-8
- `decimal_separator`: optional, string, max 1 character
- `thousands_separator`: optional, string, max 1 character
- `is_symbol_first`: optional, boolean
- `is_enabled`: optional, boolean
- `is_default`: optional, boolean

### Update Currency
Same as create, but `code` uniqueness check ignores current currency.

## Business Logic

### Default Currency
When a currency is set as default (`is_default = true`), all other currencies are automatically set to `is_default = false`.

### Delete Protection
The default currency cannot be deleted. Attempting to delete it will throw a `RuntimeException` with the message from `currency::currency.cannot_delete_default`.

## Usage Example

```php
use Molitor\Currency\Models\Currency;

// Create a currency
$currency = Currency::create([
    'code' => 'EUR',
    'name' => 'Euro',
    'symbol' => '€',
    'decimals' => 2,
    'is_enabled' => true
]);

// Get all enabled currencies
$currencies = Currency::where('is_enabled', true)->get();

// Get default currency
$default = Currency::where('is_default', true)->first();
```

## Frontend Integration

This backend API is designed to work with the `vue-currency` frontend package located at `/resources/js/packages/vue-currency`.

## Testing

The API routes can be tested using tools like:
- Postman
- curl
- Laravel Tinker
- PHPUnit tests

Example curl request:
```bash
curl -X GET \
  http://localhost/api/admin/currency/currencies \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Accept: application/json'
```

