# 🍽️ Restaurant Online Ordering System

A web-based restaurant ordering platform built with PHP, MySQL, HTML, CSS, and JavaScript. This system allows customers to browse menus, place orders online, and enables restaurant staff to manage menu items and track orders efficiently.

## 👥 Team Members

- **Elikem Awuttey** - 224DCS0201253
- **Aboagye Kelvin Owusu Ansah** - 224DCS0201258
- **Seidu Anyagre Latif** - 225E101000126

## 📋 Project Overview

This project aims to digitize and streamline the restaurant ordering process by providing:
- Online menu browsing with search and filter capabilities
- Shopping cart functionality for easy order management
- Secure user authentication and order tracking
- Admin dashboard for menu and order management
- Real-time order status updates

## ✨ Features

### Customer Features
- 🔐 User registration and login
- 📖 Browse menu with categories and search
- 🛒 Add items to cart and manage quantities
- 💳 Place orders with delivery information
- 📜 View order history and status
- 🔍 Search for specific menu items

### Admin Features
- 📊 Admin dashboard with order overview
- ➕ Add, edit, and delete menu items
- 📁 Category management
- 📦 View and manage all orders
- 🔄 Update order status (pending → confirmed → preparing → delivered)
- 📸 Upload menu item images

## 🛠️ Technology Stack

| Component | Technology |
|-----------|-----------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Server | XAMPP (Apache) |
| Version Control | Git & GitHub |

## 📁 Project Structure

```
php-restaurant-ordering-system/
├── admin/              # Admin panel
├── assets/             # CSS, JS, images
├── config/             # Configuration files
├── includes/           # Reusable components
├── auth/               # Authentication
├── customer/           # Customer pages
├── actions/            # Backend handlers
├── database/           # SQL files
├── uploads/            # Uploaded images
└── index.php           # Homepage
```

## 🚀 Getting Started

### Prerequisites

- XAMPP (or WAMP/MAMP)
- Web browser
- Text editor (VS Code, Sublime Text, etc.)
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/0592214309/php-restaurant-ordering-system.git
   ```

2. **Move to XAMPP htdocs**
   ```bash
   # Windows
   move php-restaurant-ordering-system C:\xampp\htdocs\
   
   # Linux/Mac
   mv php-restaurant-ordering-system /opt/lampp/htdocs/
   ```

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

4. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create new database: `restaurant_ordering`
   - Import SQL file: `database/restaurant_ordering.sql`

5. **Configure Database Connection**
   - Copy `config/database.example.php` to `config/database.php`
   - Update database credentials if needed

6. **Access the Application**
   - Customer site: `http://localhost/php-restaurant-ordering-system/`
   - Admin panel: `http://localhost/php-restaurant-ordering-system/admin/`

### Default Admin Login
- **Email:** admin@restaurant.com
- **Password:** admin123

## 📊 Database Schema

### Main Tables
- `users` - Store customer and admin accounts
- `categories` - Food categories
- `menu_items` - Menu items with prices and images
- `orders` - Customer orders
- `order_items` - Individual items in orders

## 🔒 Security Features

- Password hashing using PHP `password_hash()`
- Prepared statements to prevent SQL injection
- Session management for authentication
- Input validation and sanitization
- CSRF protection on forms

## 📱 Screenshots

*Coming soon...*

## 🧪 Testing

Each development phase includes:
- Functional testing
- Input validation testing
- Security testing
- Cross-browser testing
- Responsive design testing

## 📈 Development Roadmap

- [x] Phase 1: Project Setup & Database Design
- [ ] Phase 2: Authentication System
- [ ] Phase 3: Menu Display
- [ ] Phase 4: Shopping Cart
- [ ] Phase 5: Order Placement
- [ ] Phase 6: Admin Menu Management
- [ ] Phase 7: Admin Order Management
- [ ] Phase 8: UI/UX Enhancement
- [ ] Phase 9: Security & Validation
- [ ] Phase 10: Testing & Documentation

## 🤝 Contributing

This is an academic project. Team members should:
1. Create a new branch for each feature
2. Make commits with clear messages
3. Test before pushing
4. Create pull requests for review

## 📝 License

This project is for educational purposes only.

## 📞 Contact

For questions or issues, contact any team member.

## 🙏 Acknowledgments

- W3Schools for PHP tutorials
- Tutorial Republic for web development guides
- GeeksforGeeks for project inspiration
- Course instructors and mentors

---

**Last Updated:** November 2025