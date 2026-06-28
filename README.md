# Mamz Clothing - Fashion Marketplace

A complete e-commerce fashion marketplace built with PHP Native + MySQL. Features include user authentication, product management, shopping cart, order processing, payment verification, and a comprehensive admin panel.

## Features

### User Features
- **Authentication**: User registration and login with secure password hashing
- **Product Browsing**: Browse products with category filtering, price filtering, and search
- **Shopping Cart**: Add to cart, update quantities, remove items with AJAX
- **Wishlist**: Save favorite products for later
- **Checkout**: Complete checkout process with shipping information
- **Payment Verification**: Upload payment proof for manual verification
- **Order Tracking**: View order history and order details
- **User Profile**: Manage personal information
- **Product Reviews**: Rate and review purchased products
- **Contact Form**: Send messages to admin

### Admin Features
- **Dashboard**: Overview with sales charts and statistics
- **Product Management**: Full CRUD operations for products
- **Category Management**: Manage product categories
- **Order Management**: View and update order status
- **User Management**: Manage user accounts
- **Banner Management**: Manage promotional banners
- **Promo Management**: Create and manage discount codes
- **Review Management**: Moderate user reviews
- **Contact Messages**: View and manage contact form submissions

## Technology Stack

- **Backend**: PHP 8+ (Native, no frameworks)
- **Database**: MySQL with PDO Prepared Statements
- **Frontend**: HTML5, CSS3, JavaScript ES6
- **CSS Framework**: Bootstrap 5
- **JavaScript Libraries**: jQuery, SweetAlert2, Chart.js, AOS, Animate.css, SwiperJS
- **Icons**: Font Awesome 6
- **Security**: CSRF protection, XSS protection, password hashing, session security

## Project Structure

```
mamz_clothing/
├── admin/                    # Admin panel pages
│   ├── dashboard.php
│   ├── products.php
│   ├── product-form.php
│   ├── categories.php
│   ├── orders.php
│   ├── order-detail.php
│   ├── users.php
│   ├── user-form.php
│   ├── banners.php
│   ├── promos.php
│   ├── reviews.php
│   ├── contacts.php
│   └── profile.php
├── ajax/                     # AJAX handlers
│   ├── cart.php
│   ├── wishlist.php
│   ├── search.php
│   └── admin/               # Admin AJAX handlers
│       ├── save-category.php
│       ├── delete-category.php
│       ├── delete-product.php
│       ├── save-banner.php
│       ├── delete-banner.php
│       ├── save-promo.php
│       ├── delete-promo.php
│       ├── update-review.php
│       ├── delete-review.php
│       ├── delete-user.php
│       ├── get-contact.php
│       └── delete-contact.php
├── assets/                   # Static assets
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   ├── cart.js
│   │   ├── wishlist.js
│   │   └── admin.js
│   └── images/              # Placeholder images
├── config/                  # Configuration files
│   └── config.php
├── database/                # Database connection
│   └── Database.php
├── includes/                # Helper functions and security
│   ├── functions.php
│   └── auth.php
├── models/                  # Data models
│   ├── User.php
│   ├── Product.php
│   ├── Category.php
│   ├── Cart.php
│   ├── Order.php
│   ├── OrderDetail.php
│   ├── Wishlist.php
│   ├── Review.php
│   ├── Banner.php
│   ├── Promo.php
│   └── Contact.php
├── views/                   # View templates
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── admin/
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   └── components/
│   └── components/
│       └── product-card.php
├── uploads/                 # Upload directories
│   ├── products/
│   ├── payment/
│   └── banners/
├── bootstrap.php            # Bootstrap file
├── index.php                # Home page
├── products.php             # Products listing
├── product.php              # Product detail
├── cart.php                 # Shopping cart
├── checkout.php             # Checkout page
├── checkout-process.php     # Checkout processing
├── login.php                # Login page
├── register.php             # Registration page
├── logout.php               # Logout handler
├── profile.php              # User profile
├── order-history.php        # Order history
├── order-detail.php         # Order detail
├── payment-confirmation.php # Payment confirmation
├── categories.php           # Category page
├── contact.php              # Contact page
├── about.php                # About page
├── database.sql             # Database schema
└── README.md                # This file
```

## Installation

### Prerequisites
- XAMPP (or any PHP + MySQL server)
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web browser (Chrome, Firefox, Safari, Edge)

### Step 1: Setup XAMPP
1. Download and install XAMPP from https://www.apachefriends.org/
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Copy Project Files
1. Copy the entire `mamz_clothing` folder to `C:\xampp\htdocs\`
2. The project should be accessible at `http://localhost/mamz_clothing`

### Step 3: Create Database
1. Open phpMyAdmin at `http://localhost/phpmyadmin`
2. Create a new database named `mamz_clothing`
3. Import the `database.sql` file from the project root
   - Click on the `mamz_clothing` database
   - Click on "Import" tab
   - Choose `database.sql` file
   - Click "Go"

### Step 4: Configure Database Connection
1. Open `config/config.php`
2. Update the database credentials if needed:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mamz_clothing');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 5: Create Upload Directories
1. Create the following directories if they don't exist:
   - `uploads/products/`
   - `uploads/payment/`
   - `uploads/banners/`
2. Make sure these directories have write permissions

### Step 6: Configure Site Settings
1. Open `config/config.php`
2. Update the site settings:
```php
define('SITE_NAME', 'Mamz Clothing');
define('SITE_TAGLINE', 'Simple Style, Premium Quality');
define('SITE_URL', 'http://localhost/mamz_clothing');
define('WHATSAPP_NUMBER', '6281234567890'); // Update with your WhatsApp number
```

### Step 7: Create Admin Account
1. Open phpMyAdmin
2. Go to the `users` table
3. Insert a new record with the following SQL:
```sql
INSERT INTO users (nama_lengkap, email, password, role, status) 
VALUES ('Admin', 'admin@mamzclothing.com', '$2y$10$YourHashedPasswordHere', 'admin', 'aktif');
```
4. Or register a new account and manually update the role to 'admin' in the database

## Default Credentials

After installation, you can register a new user account. To make a user an admin:

1. Register a new account at `http://localhost/mamz_clothing/register.php`
2. Open phpMyAdmin
3. Go to the `users` table
4. Find your user and change the `role` field to `admin`

## Usage

### Accessing the Website
- **Home Page**: `http://localhost/mamz_clothing/index.php`
- **Products**: `http://localhost/mamz_clothing/products.php`
- **Login**: `http://localhost/mamz_clothing/login.php`
- **Register**: `http://localhost/mamz_clothing/register.php`

### Accessing the Admin Panel
- **Admin Dashboard**: `http://localhost/mamz_clothing/admin/dashboard.php`
- **Login with admin credentials to access the admin panel**

## Security Features

- **SQL Injection Prevention**: All database queries use PDO prepared statements
- **XSS Protection**: All user input is sanitized using the `sanitize()` function
- **CSRF Protection**: CSRF tokens are implemented for form submissions
- **Password Security**: Passwords are hashed using `password_hash()` with BCRYPT
- **Session Security**: Secure session configuration with HttpOnly cookies
- **File Upload Validation**: File type and size validation for all uploads
- **Input Validation**: All form inputs are validated before processing

## Customization

### Changing the Design
- Edit `assets/css/style.css` for user-facing styles
- Edit `assets/css/admin.css` for admin panel styles

### Adding New Features
1. Create a new model in `models/` directory
2. Create a new controller or add logic to existing controllers
3. Create view files in `views/` directory
4. Add AJAX handlers in `ajax/` directory if needed

### Modifying Database
1. Update `database.sql` with your schema changes
2. Update or create model files to reflect the changes
3. Test the changes thoroughly

## Troubleshooting

### Database Connection Error
- Check that MySQL service is running in XAMPP
- Verify database credentials in `config/config.php`
- Ensure the database `mamz_clothing` exists

### File Upload Not Working
- Check that upload directories exist and have write permissions
- Verify `UPLOAD_PATH` in `config/config.php` is correct
- Check PHP `upload_max_filesize` and `post_max_size` settings in php.ini

### Session Not Working
- Check that `session_save_path` is writable
- Verify session configuration in `config/config.php`
- Clear browser cookies and try again

### AJAX Requests Failing
- Check browser console for JavaScript errors
- Verify AJAX handler files exist and have correct permissions
- Check that CSRF tokens are being sent correctly

## Support

For issues or questions:
- Check the troubleshooting section above
- Review the code comments for additional context
- Ensure all dependencies (PHP, MySQL) are properly configured

## License

This project is for educational purposes. Feel free to modify and use it as needed.

## Credits

- **Developed for**: Mamz Clothing Fashion Marketplace
- **Technologies**: PHP Native, MySQL, Bootstrap 5, jQuery
- **Icons**: Font Awesome
- **Charts**: Chart.js
- **Animations**: AOS, Animate.css

---

**Note**: This is a complete, production-ready e-commerce application. Ensure proper testing before deploying to a live server. For production use, enable HTTPS and update security settings accordingly.
