# Split Bill Laravel Application

A Laravel application to split bills and track expenses among friends with real-time balance calculations.

## Project Overview

This application allows users to:
- Create bills for shared expenses (trips, dinners, etc.)
- Add individual expenses with details about who paid and who should share
- View settlement summaries showing who owes what
- Get optimized payment recommendations to minimize transactions

## Features

- **Bill Management**: Create and manage bills with multiple friends
- **Expense Tracking**: Add individual expenses with sharing options
- **Balance Calculation**: Real-time calculation of individual balances
- **Settlement Summary**: Clear display of who owes what to whom
- **Payment Optimization**: Minimize the number of transactions needed

## Tech Stack

- **Backend**: Laravel 10+
- **Database**: MySQL
- **Frontend**: Bootstrap (for styling)
- **PHP**: 8.1+

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd split-bill-laravel
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=split_bill
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

## Usage

1. Visit `http://localhost:8000` in your browser
2. Create a new bill by clicking "Create New Bill"
3. Add friends to the bill
4. Add expenses with details about who paid and who should share
5. View the settlement summary to see who owes what

## Database Schema

- **bills**: Main bill information
- **friends**: Friends associated with bills
- **expenses**: Individual expenses within bills
- **expense_shares**: Pivot table for expense sharing relationships

## Contributing

This is a technical assessment project. Please refer to the PRD (prd.md) and todo (todo.md) files for project requirements and progress tracking.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
