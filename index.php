<?php
session_start();
require_once 'config.php';

$error = '';
$debug_sql = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // VULNERABLE CODE: No sanitization!
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Constructing the query directly with user input
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $debug_sql = $sql; // Save for display

    // Execute multi_query to allow stacked queries if supported (often disabled by default in PHP driver involved, but good for demonstrating intent)
    // For standard injection we just need query()
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: songs.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1><span style="color:var(--terminal-green)">></span> System Login</h1>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" autocomplete="off">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" autocomplete="off">

                <button type="submit">Authenticate</button>
            </form>

            <?php if($debug_sql): ?>
                <div class="sql-log">
                    <span class="sql-label">DEBUG: Executed SQL Query</span>
                    <?php echo htmlspecialchars($debug_sql); ?>
                </div>
            <?php endif; ?>
            
            <p style="margin-top: 20px; font-size: 0.9em; color: #8b949e;">
                <i>Tip: Try <b>admin' --</b> or <b>' OR '1'='1</b></i>
            </p>
        </div>
    </div>
</body>
</html>
