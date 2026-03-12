## Installation Steps

1. Clone the repository

git clone https://github.com/sumit-0120/project-approval-workflow.git

2. Install dependencies

composer install

3. Setup environment

copy .env.example .env

4. Generate key

php artisan key:generate

5. Run migrations

php artisan migrate

6. Create Stored Procedures

After running migrations, open phpMyAdmin from XAMPP.

Select the project database and go to the SQL tab, then run the following queries:


# this is sql command for PROCEDURE sp_approve_project

DELIMITER $$

CREATE PROCEDURE sp_approve_project(IN pid INT, IN uid INT)
BEGIN

UPDATE projects
SET status = 'approved'
WHERE id = pid;

INSERT INTO approvals(project_id, admin_id, status, created_at, updated_at)
VALUES (pid, uid, 'approved', NOW(), NOW());

INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
VALUES (uid, pid, 'approved', NOW(), NOW());

END $$

DELIMITER;

# this is sql command for PROCEDURE sp_reject_project

DELIMITER $$

CREATE PROCEDURE sp_reject_project(
    IN pid INT,
    IN uid INT,
    IN reason TEXT
)
BEGIN

UPDATE projects
SET status = 'rejected',
    updated_at = NOW()
WHERE id = pid;

INSERT INTO approvals(project_id, admin_id, status, reason, created_at, updated_at)
VALUES (pid, uid, 'rejected', reason, NOW(), NOW());

INSERT INTO audit_logs(user_id, project_id, action, created_at, updated_at)
VALUES (uid, pid, 'rejected', NOW(), NOW());

END $$

DELIMITER ;


7. Run Seeder

php artisan db:seed

8. Start the server

    php artisan serve

## Demo Credentials

Admin Login  
Email: admin@gmail.com  
Password: sR12345
