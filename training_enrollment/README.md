# Training Enrollment System

A multi-table, database-driven web application built with PHP and PDO for managing courses, class schedules, students, and enrollments.

---

## 1. Project Description
This system models a training program enrollment domain. An Administrator can:
- **Manage Courses**: Add, edit, delete, and list course offerings.
- **Manage Classes**: Schedule classes under specific courses and allocate limited slots.
- **Record & Enroll Students**: Add a new student and enroll them into a class in a single atomic transaction.
- **Enroll Existing Students**: Enroll an existing registered student into a class.
- **Cancel Enrollments**: Cancel active enrollments and automatically restore available slots inside a transaction.
- **Generate Reports**: View enrollment summary by course and class showing active, cancelled, and remaining slot counts.

Data consistency is enforced using **PDO transactions** with slot validation and rollback safeguards.

---

## 2. Setup Instructions

1. **Start Apache & MySQL**: Open XAMPP Control Panel and start Apache and MySQL services.
2. **Database Import**:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`) or MySQL CLI.
   - Import `sql/schema.sql` (or `training_db.sql`) to create the `training_db` database and initial tables/sample data.
3. **Database Configuration**:
   - Check `config/db.php`. Default settings:
     - Host: `localhost`
     - Database: `training_db`
     - User: `root`
     - Password: `""` (blank)
4. **Access Application**:
   - Open your browser and navigate to:
     `http://localhost/training_enrollment/`

---

## 3. Design Patterns Explanation

### Singleton Pattern (`Database.php`)
- **What it is**: The `Database` class extends `PDO` and restricts instantiation by keeping its constructor private and providing a static `getInstance()` method.
- **Why it was used**: Ensures that only a single database connection instance is created and shared across the entire request lifecycle. This prevents excessive database connection overhead and resource leakage.

### Repository Pattern (`EnrollmentRepository.php`)
- **What it is**: Mediates between the domain logic and the data mapping layer, exposing high-level domain operations (`recordStudent()`, `enroll()`, `cancel()`, `allWithDetails()`).
- **Why it was used**: Encapsulates complex multi-table queries and transactional logic (`beginTransaction()`, slot availability checks, inserts, slot decrements/restores, and `commit()` / `rollBack()`) away from the presentation/controller pages (`students.php`, `enroll.php`, `enrollments.php`).

---

## 4. Database Schema
- **courses**: `course_id` (PK), `course_code` (Unique), `course_name`, `description`
- **classes**: `class_id` (PK), `course_id` (FK), `class_code`, `schedule`, `instructor`, `slots`
- **students**: `student_id` (PK), `full_name`, `email`, `phone`, `created_at`
- **enrollments**: `enrollment_id` (PK), `student_id` (FK), `class_id` (FK), `enrollment_date`, `status`
