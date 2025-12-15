<?php
session_start();
require_once 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Handle New Post (VULNERABLE: No input sanitization)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $username = $_POST['username'] ? $_POST['username'] : 'Anonymous';
    $message = $_POST['message'];
    
    // Using prepared statements for SQL Injection protection (we are testing XSS here, not SQLi)
    // BUT we are NOT sanitizing the input HTML/JS
    $stmt = $conn->prepare("INSERT INTO posts (username, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $message);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to prevent form resubmission
    header("Location: xss.php");
    exit;
}

// Handle Delete Post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: xss.php");
    exit;
}

// Fetch all posts (Newest first)
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook - XSS Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div style="text-align: right; margin-bottom: 10px; color: #8b949e;">
            <span>Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
        </div>

        <div class="card">
            <h1><span style="color:#e9d36c;">></span> Vulnerable Guestbook</h1>
            <p>Write a message. HTML and JavaScript are allowed (for educational purposes).</p>

            <form method="POST" action="">
                <input type="text" name="username" placeholder="Your Name (optional)" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                <textarea name="message" rows="4" placeholder="Write your message here..." style="width: 100%; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 6px; padding: 10px; margin-bottom: 15px; box-sizing: border-box;"></textarea>
                <button type="submit">Sign Guestbook</button>
            </form>
        </div>

        <div class="card">
            <h3>Recent Messages</h3>
            <div class="post-list">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="post-item">
                            <div class="post-meta">
                                <span>
                                    <b><?php echo htmlspecialchars($row['username']); ?></b> 
                                    at <?php echo $row['created_at']; ?>
                                </span>
                                <a href="xss.php?delete=<?php echo $row['id']; ?>" class="btn-delete" style="color: white; text-decoration: none;">Delete</a>
                            </div>
                            <!-- VULNERABLE: Outputting content directly without htmlspecialchars() -->
                            <div class="post-content"><?php echo $row['content']; ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
