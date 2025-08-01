# Split Bill Between Friends - Requirements Document

## Project Overview
- **Purpose:** Laravel technical assessment project
- **Timeline:** Interview submission
- **Tech Stack:** Laravel, MySQL, Bootstrap/Tailwind (optional)
- **AI Tool:** Cursor IDE with AI assistance

## Core User Stories

### 1. Create Bill
**As a user, I want to create a new bill so I can track shared expenses**

- Input bill name (e.g., "Goa Trip", "Dinner at XYZ")
- Add friends by name/email
- Set up initial bill structure

### 2. Add Expenses
**As a user, I want to add individual expenses to track who paid what**

- Add expense with: item name, amount, paid by person
- Select which friends share this expense (checkboxes)
- how much each of them paid
- Support multiple expenses per bill

### 3. View Settlement Summary
**As a user, I want to see who owes what to settle the bill**

- Individual balances (how much each person spent vs owes)
- Clear settlement instructions (who pays whom)
- Net amounts calculation

## Technical Requirements

### Database Schema

```sql
-- bills table
id | name | created_at | updated_at

-- friends table  
id | name | bill_id | created_at | updated_at

-- expenses table
id | bill_id | title | amount | paid_by (friend_id) | created_at

-- expense_shares table (pivot)
id | expense_id | friend_id | created_at
```

### Laravel Features Required

- MVC structure (Models, Views, Controllers)
- Eloquent relationships (hasMany, belongsToMany)
- Migrations for database setup
- Form validation (server-side)
- Blade templating
- Route management

## User Interface Requirements

### Page Structure

#### Dashboard/Home Page
- "Create New Bill" button (prominent)
- List existing bills with summary info
- Clean, simple navigation and responcive

#### Bill Detail Page
- Bill overview section
- Add expense form
- Expenses list (with edit/delete)
- Settlement summary

#### Settlement Display
- Individual balances section
- Optimal settlement recommendations
- Clear, readable format

### Forms Needed

#### Create Bill Form
- Bill name (required)
- Add friends (dynamic - can add multiple)
- Form validation

#### Add Expense Form
- Expense title (required)
- Amount (required, numeric)
- Paid by (dropdown of bill friends)
- Shared by (checkboxes for friends)
- Form validation

## Business Logic Requirements

### Calculation Logic

- **Individual Spending:** Track how much each person paid
- **Individual Share:** Calculate how much each person should pay based on shared expenses
- **Net Balance:** Determine who owes money vs who should receive money
- **Settlement Optimization:** Minimize number of transactions needed

### Example Calculation

- Alice pays ₹600 for dinner (shared by all 3)
- Bob pays ₹300 for snacks (shared by Bob + Charlie only)

**Result:**
- **Alice:** Paid ₹600, Owes ₹200 → Net: +₹400 (should receive)
- **Bob:** Paid ₹300, Owes ₹350 → Net: -₹50 (should pay)
- **Charlie:** Paid ₹0, Owes ₹350 → Net: -₹350 (should pay)

## AI Usage Documentation

### Using Cursor IDE for:
- Boilerplate code generation (migrations, models)
- Form validation logic
- Bootstrap/CSS styling assistance
- Debugging complex calculation logic

### Manual implementation:
- Business logic design decisions
- Database relationship planning
- Settlement algorithm logic
- User experience flow

## Success Criteria

- [ ] All core requirements implemented
- [ ] Accurate calculation logic
- [ ] Clean, readable code with comments
- [ ] Proper Laravel conventions followed
- [ ] User-friendly interface
- [ ] Form validation working
- [ ] Settlement summary clearly displayed
- [ ] AI usage properly documented

## Technical Considerations

- Use decimal/float precision carefully for money calculations
- Implement proper error handling for edge cases
- Ensure relationships are properly set up (foreign keys, constraints)
- Follow Laravel naming conventions
- Keep controllers lean, move business logic to models/services
- Use Laravel collections for data manipulation

## Submission Requirements

- Clean, well-structured Laravel project
- README with setup instructions
- Documentation of AI-assisted vs manual code
- Screenshots of working application
- Database seeder with sample data (optional but helpful)