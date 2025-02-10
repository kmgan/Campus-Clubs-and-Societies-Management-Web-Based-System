
# Campus-Clubs-and-Societies-Management-Web-Based-System

This document provides step-by-step instructions for setting up and running the system.

## Prerequisites

Ensure you have the following software installed:

- MySQL Server
- PHP
- Composer
- Visual Studio Code (VSCode)

Additionally, install any required dependencies for the system.

## Step 1: Modify PHP Configuration

Before proceeding, update the `php.ini` file to accommodate larger file uploads:

1. Open your `php.ini` file.
2. Update the following settings:

```ini
upload_max_filesize = 10M
post_max_size = 100M
```

3. Restart your PHP server to apply the changes.

## Step 2: Set Up the Database

1. Open a terminal.
2. Import the database using the provided self-contained dump file.

Using MySQL CLI:
```bash
mysql -u [username] -p [database_name] < [path_to_dump_file]
```

Replace the placeholders:

- `[username]` with your MySQL username.
- `[database_name]` with the name of the database you want to import to.
- `[path_to_dump_file]` with the full path to the dump file.

## Step 3: Configure Database Connection

1. Open the project folder in VSCode.
2. Locate the database configuration file (e.g., `.env` or `config/database.php`).
3. Ensure the database connection settings match your setup.

Example configuration for `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=[database_name]
DB_USERNAME=[username]
DB_PASSWORD=[password]
```

Replace the placeholders with your actual database credentials.

## Step 4: Run the Application

1. Open a terminal in VSCode.
2. Navigate to the root directory of your project.
3. Run the following command to start the server:

```bash
php artisan serve
```

The application will be accessible at the URL displayed in the terminal (e.g., `http://127.0.0.1:8000`).

## Step 5: Access the Admin Panel

1. Open a web browser and go to the application URL.
2. Log in using the following credentials:
   - **Username:** admin
   - **Password:** password123
