# Virtual Academy: Online Learning Platform
## The final code of the projesis on the branch "final"
## Project Overview
The **Virtual Academy** is an online learning platform that allows users to register, browse, and enroll in courses. The platform offers a wide range of courses with video tutorials, support materials, and interactive assessments (optional). It supports two main user roles: **Students** and **Administrators**.

- **Students** can:
  - Sign up and create a personal profile.
  - Browse and enroll in available courses.
  - Watch video tutorials and download support materials.
  - Provide comments and ratings for courses.
  
- **Administrators** can:
  - Manage the platform's content.
  - Create, update, and delete courses and modules.
  - Monitor student enrollments and track progress.
  
The platform is built using a  **React** frontend and a **PHP/MySQL** backend.

## Features
- User authentication (sign up, log in, log out)
- Course browsing and filtering by category
- Video tutorials and downloadable support materials
- Commenting and rating system for courses
- Admin dashboard for content management
- Optional: Automated assessments and quizzes

## Technologies Used
### Backend
- PHP (business logic and API)
- MySQL (database for storing user data, courses, comments, etc.)

### Frontend
- React (user interface and client-side functionality)
- HTML, CSS, JavaScript (for styling and interactivity)

## Requirements
To run the project locally, you will need the following:

- **PHP 7.4+**
- **MySQL 5.7+**
- **Node.js** (for running the React frontend)
- **Apache/Nginx** (web server)

## Getting Started

### Backend Setup (PHP + MySQL)
1. Clone the repository:
    ```bash
    git clone https://github.com/AkramHamd/Virtual-Academy.git
    cd virtual-academy/backend
    ```

2. Create the MySQL database:
    ```sql
    CREATE DATABASE virtual_academy;
    ```

3. Import the database schema (in the `sql` folder):
    ```bash
    mysql -u root -p virtual_academy < sql/schema.sql
    ```

4. Configure database connection:
    - Edit the `db_connection.php` file with your database credentials:
    ```php
    $conn = new mysqli('localhost', 'your-username', 'your-password', 'virtual_academy');
    ```

5. Start the PHP development server:
    ```bash
    php -S localhost:8000
    ```

### Frontend Setup (React)
1. Navigate to the frontend directory:
    ```bash
    cd ../frontend
    ```

2. Install the dependencies:
    ```bash
    npm install
    ```

3. Start the React development server:
    ```bash
    npm start
    ```

4. Open your browser and go to `http://localhost:3000` to view the application.

## API Endpoints

### User Authentication
- **POST** `/api/register`: Register a new user
- **POST** `/api/login`: Log in with an existing account
- **POST** `/api/logout`: Log out

### Courses
- **GET** `/api/courses`: Get a list of available courses
- **GET** `/api/courses/:id`: Get details of a specific course
- **POST** `/api/courses`: Create a new course (Admin only)
- **PUT** `/api/courses/:id`: Update an existing course (Admin only)
- **DELETE** `/api/courses/:id`: Delete a course (Admin only)

### Enrollments
- **POST** `/api/enroll`: Enroll a student in a course

### Comments & Ratings
- **POST** `/api/courses/:id/comments`: Add a comment and rating to a course.

