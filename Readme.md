# SQL Injection Test Lab

## 1. Database Setup

Before running the application, you need to configure the database.

1.  **Create and Seed the Database**:
    Run the `db_schema.sql` script to create the `sql_injection_lab` database and populate it with test data.
    
    You can do this via the command line (assuming you have the `mysql` client installed):
    ```bash
    mysql -u root -p < db_schema.sql
    ```
    *Enter your MySQL password when prompted.*

2.  **Configure Credentials**:
    Open `config.php` and update the database connection settings if they differ from your local setup:
    -   **DB_HOST**: `127.0.0.1`
    -   **DB_PORT**: `3307` (Default is often 3306, check your MySQL server)
    -   **DB_USER**: `root`
    -   **DB_PASS**: `root`

## 2. Start the Server

Once the database is ready, you can start the PHP built-in server.

1.  **Run the Server**:
    Execute the following command in the project root directory:
    ```bash
    php -S localhost:8000
    ```

2.  **Access the Application**:
    Open your web browser and navigate to:
    [http://localhost:8000](http://localhost:8000)

## Troubleshooting
-   If you see a "Connection failed" error, double-check your credentials in `config.php` and ensure your MySQL server is running.
-   If the port `8000` is already in use, try a different port (e.g., `php -S localhost:8080`).
