# Split Bill Laravel Application

A Laravel-based web application for splitting bills and tracking shared expenses among friends. This project demonstrates modern Laravel development practices including service classes, form requests, and complex business logic implementation.

## 🚀 Features

- **Bill Management**: Create and manage bills with multiple friends
- **Expense Tracking**: Add, edit, and delete shared expenses
- **Smart Calculations**: Automatic calculation of individual spending, shares, and net balances
- **Settlement Optimization**: Optimal settlement plan using greedy algorithm to minimize transactions
- **Bill Settlement**: Mark bills as settled to prevent further modifications
- **Responsive UI**: Clean, mobile-friendly interface built with Tailwind CSS
- **Real-time Updates**: Dynamic forms and confirmation modals

## 📋 Prerequisites

Before running this application, ensure you have the following installed on your system:

- **PHP >= 8.1** with extensions:
  - BCMath PHP Extension
  - Ctype PHP Extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
- **Composer** (latest version)
- **Node.js >= 16.x** and **npm**
- **MySQL >= 5.7** or **SQLite** (for development)
- **Git**

## 🛠️ Installation & Setup

Follow these steps to get the application running on your local machine:

### 1. Clone the Repository

```bash
git clone <repository-url>
cd split-bill-laravel
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit the `.env` file and configure your database settings:

```env
# For MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=split_bill
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For SQLite (simpler for testing)
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite
```

**For SQLite (recommended for testing):**
```bash
# Create SQLite database file
touch database/database.sqlite

# Update .env file
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite
```

### 5. Run Database Migrations

```bash
# Create database tables
php artisan migrate


### 6. Install Frontend Dependencies

If you encounter permission errors with npm install, use this fix:

```bash
# Fix for npm permission errors
rm -rf node_modules
rm package-lock.json
npm install
```

### 7. Build Frontend Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 8. Start the Development Server

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

## 🎯 Quick Start Guide

### Creating Your First Bill

1. **Access the Application**: Navigate to `http://localhost:8000`
2. **Create New Bill**: Click "Create New Bill" button
3. **Add Bill Details**: 
   - Enter a bill name (e.g., "Weekend Trip")
   - Add friend names (minimum 1 required)
4. **Add Expenses**: 
   - Click "Add Expense" on the bill details page
   - Fill in expense details (title, amount, who paid, who shares)
5. **View Settlement**: See automatic calculations and optimal settlement plan
6. **Settle Bill**: Click "Settle Bill" when all payments are complete

### Understanding the Settlement Logic

The application automatically calculates:
- **Individual Spending**: How much each person paid
- **Fair Shares**: How much each person should pay based on shared expenses
- **Net Balances**: Who owes money (negative) and who should receive money (positive)
- **Optimal Settlement**: Minimum number of transactions needed to settle all debts

## 📁 Project Structure

```
split-bill-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # HTTP Controllers
│   │   └── Requests/             # Form Request validation
│   ├── Models/                   # Eloquent Models
│   └── Services/                 # Business Logic Services
├── database/
│   ├── migrations/               # Database schema
│   └── seeders/                  # Sample data
├── resources/
│   ├── views/                    # Blade templates
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript
├── routes/
│   └── web.php                   # Application routes
└── public/                       # Public web files
```

### Key Components

- **BillCalculationService**: Complex mathematical calculations for bill splitting
- **ExpenseService**: Business logic for expense management
- **StoreExpenseRequest**: Centralized validation for expense forms
- **Models**: Bill, Friend, Expense with proper relationships

## 🧪 Testing the Application

### Sample Test Scenarios

1. **Basic Bill Splitting**:
   - Create bill with 3 friends: Alice, Bob, Charlie
   - Add expense: "Dinner $90" paid by Alice, shared by all 3
   - Verify: Alice should receive $60, Bob and Charlie each owe $30

2. **Complex Splitting**:
   - Add multiple expenses with different sharing patterns
   - Verify settlement plan minimizes total transactions

3. **Bill Settlement**:
   - Test that settled bills prevent further expense modifications
   - Verify UI changes appropriately for settled bills

## 🔧 Troubleshooting

### Common Issues

**1. Permission Errors with npm**
```bash
rm -rf node_modules
rm package-lock.json
npm install
npm run dev
```

**2. Database Connection Issues**
- Verify database credentials in `.env`
- Ensure database server is running
- For SQLite, check file path and permissions

**3. PHP Extension Missing**
```bash
# Check PHP extensions
php -m

# Install missing extensions (Ubuntu/Debian)
sudo apt-get install php-xml php-mbstring php-bcmath
```

**4. Composer Issues**
```bash
# Clear composer cache
composer clear-cache
composer install --no-cache
```

**5. Route Cache Issues**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

## 🏗️ Development Notes

### Architecture Highlights

- **Service Layer Pattern**: Business logic separated from controllers
- **Form Request Validation**: Centralized validation logic
- **Eloquent Relationships**: Proper model relationships with eager loading
- **Money Handling**: Precise decimal calculations to avoid floating-point errors
- **Algorithm Implementation**: Greedy algorithm for optimal debt settlement

### Code Quality Features

- **PHPDoc Comments**: Comprehensive documentation
- **Error Handling**: Graceful error handling with user-friendly messages
- **Validation**: Robust form validation with custom rules
- **Security**: CSRF protection, SQL injection prevention
- **Performance**: Eager loading to prevent N+1 queries

## 📝 API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/` | Dashboard - List all bills |
| GET | `/bills/create` | Show create bill form |
| POST | `/bills` | Store new bill |
| GET | `/bills/{bill}` | Show bill details |
| POST | `/bills/{bill}/expenses` | Add expense to bill |
| DELETE | `/bills/{bill}/expenses/{expense}` | Delete expense |
| POST | `/bills/{bill}/settle` | Settle bill |

## 🚀 Production Deployment

For production deployment:

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database credentials
4. Run `composer install --optimize-autoloader --no-dev`
5. Run `npm run build`
6. Set up proper web server configuration (Apache/Nginx)
7. Configure SSL certificate
8. Set up database backups

## 👨‍💻 Development Approach

This project demonstrates proficiency in:
- Modern Laravel development patterns
- Complex business logic implementation
- Database design and relationships
- Frontend integration with Alpine.js
- Code organization and documentation
- Testing and validation strategies

### 🤖 AI Assistance & Development Process

This project was developed with a combination of manual coding and AI assistance to demonstrate modern development workflows:

**AI-Assisted Components:**
- Initial boilerplate code generation (migrations, model structure)
- Form validation rules and error messages
- Frontend styling and responsive design implementation
- Code documentation and PHPDoc comments

**Manual Development & Design Decisions:**
- Database schema design and relationship planning
- Business logic architecture (Service layer pattern)
- Complex bill splitting algorithm design and implementation
- Settlement optimization logic (greedy algorithm approach)
- User experience flow and interface design decisions
- Error handling strategies and edge case management
- Code refactoring and quality improvements

**Problem-Solving & Debugging:**
- Settlement calculation accuracy and floating-point precision handling
- Frontend permission issues and npm troubleshooting
- Performance optimization with eager loading
- Security considerations and validation logic

The AI assistance primarily helped with code generation and documentation, while the core architectural decisions, algorithm design, and problem-solving approaches were manually planned and implemented.

## 📄 License

This project is created for interview/demonstration purposes.

---

**Need Help?** If you encounter any issues during setup, please check the troubleshooting section above or verify that all prerequisites are properly installed.