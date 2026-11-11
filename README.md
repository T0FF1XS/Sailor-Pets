# Sailors Pet Supplies

## Mga wala pa na functions

### Categories
- `controllers/CategoryController.php`
- `models/Category.php`
- `views/categories/index.php`

### Staff accounts
- `controllers/StaffController.php`
- `models/User.php`
- `views/staff/index.php`

### Storage rooms
- `controllers/RoomController.php`
- `models/Room.php`
- `views/rooms/index.php`

### Sales at resibo
- `controllers/SaleController.php`
- `models/Sale.php`
- `views/sales/index.php`

### Sensors page
- `controllers/SensorController.php`
- `models/Sensor.php`
- `views/sensors/index.php`

### Mga file

- `index.php` — Front controller. Lahat ng request dito dumadaan. Binabasa ang `?page=...` at tinatawag ang tamang controller (nasa `$routes` na listahan)
- `config.php` — Settings lang (database, BASE_URL, sensor key, owner account)
- `core/database.php` — Koneksyon sa database, `run()`, `get_rows()`, `get_row()` at auto-setup ng tables
- `core/helpers.php` — Mga maliit na function: `e()`, `url()`, `redirect()`, `flash()`, `require_role()`, `photo_url()`, `render()`
- `models/` — Ang mga SQL query. Isang file kada topic: `Product`, `Category`, `Inquiry`, `Sale`, `StockIn`, `Alert`, `Sensor`, `Room`, `User`, `LoginAttempt`
- `controllers/` — Ang logic ng bawat page: `StoreController`, `AuthController`, `DashboardController`, `InquiryController`, `PosController`, `SaleController`, `ProductController`, `CategoryController`, `StaffController`, `RoomController`, `SensorController`. Kumukuha sila ng data sa model, tapos tinatawag ang `render()` para ipakita ang view
- `views/` — Ang HTML lang. `layout/header.php` at `layout/footer.php` ang frame ng lahat ng page. May folder kada page: `store/`, `auth/`, `dashboard/`, `inquiries/`, `pos/`, `sales/`, `products/`, `categories/`, `staff/`, `rooms/`, `sensors/`


## Photos
- `assets/images/products/` — isang photo kada item, pangalan = code ng item (`TF-0088.jpg`). Lahat ng photos ay nasa iisang folder, hindi na kailangan ang category folders
- `assets/images/categories/` — isang photo kada category, pangalan = category name na lowercase may dash (`dog-food.jpg`, `anti-tick-flea.jpg`, `ear-eye-drops.jpg`)
- Allowed: jpg, jpeg, png, webp
- Walang photo ang item? Category photo ang lalabas. Walang category photo? Icon ang lalabas.
