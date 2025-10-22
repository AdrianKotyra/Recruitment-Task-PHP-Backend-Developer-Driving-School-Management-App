# Recruitment Task: PHP Backend Developer – PlanJazd.pl

This project is a recruitment task for the **PHP Backend Developer** 
The goal is to implement a simple backend in **pure PHP** for a driving school management web application.  
The provided `index.php` file contains a complete frontend (HTML, CSS, JS) that communicates with a backend via `fetch()` requests.

---

## 🧩 Task Overview

Your job is to build the missing backend logic in **plain PHP**, connected to a **MySQL** database using **PDO**.

### Required Features
- **GET /rides** – Return a list of all scheduled driving sessions (JSON)
- **POST /rides** – Add a new driving session (validate and save to DB)
- **DELETE /rides/{id}** – Remove a driving session by ID

---

## ⚙️ Project Structure
index.php # Provided frontend file
api.php # Backend API endpoints
config.php # Database configuration (PDO connection)
schema.sql # MySQL database structure
## 🛠️ Technical Requirements
- Pure PHP (no frameworks or ORM)
- MySQL + PDO
- Server-side validation
- Secure database queries
- Clear PHPDoc-style comments in the code

---

## 🚀 Setup Instructions

1. Import the database structure from `schema.sql` into your MySQL server.
2. Update database credentials in `config.php`.
3. Run a local PHP server:
   ```bash
   php -S localhost:8000
Open http://localhost:8000/index.php in your browser.

Test all CRUD operations through the frontend interface.
