<?php
session_start();
require_once 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$debug_sql = '';
$results = [];
$error = '';

// Get all songs for the dropdown (safe query to populate UI)
$all_songs = $conn->query("SELECT id, title FROM songs");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    
    // VULNERABLE CODE: unsanitized input
    $id = $_GET['id'];
    
    // Constructing the query directly
    $sql = "SELECT id, title, description, artist FROM songs WHERE id = $id";
    $debug_sql = $sql;

    // Execute
    try {
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
        } else {
            $error = $conn->error;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Database - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        <div style="text-align: right; margin-bottom: 10px; color: #8b949e;">
            <span>Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
        </div>

        <div class="card">
            <h1><span style="color:var(--terminal-green)">></span> Song Database</h1>
            <p>Select a song ID to view its details. Observe the query below.</p>

            <form method="GET" action="">
                <label for="id">Select Song ID:</label>
                <!-- We use a text input to make injection easier than a select dropdown -->
                <input type="text" id="id" name="id" placeholder="Enter song ID (e.g., 1)" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
                <button type="submit">Query Database</button>
            </form>

            <?php if($debug_sql): ?>
                <div class="sql-log">
                    <span class="sql-label">DEBUG: Executed SQL Query</span>
                    <?php echo htmlspecialchars($debug_sql); ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="error">
                    <strong>SQL Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($results)): ?>
                <h3>Query Results</h3>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <?php if(count($results) > 0): ?>
                                    <?php foreach(array_keys($results[0]) as $key): ?>
                                        <th><?php echo htmlspecialchars($key); ?></th>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($results as $row): ?>
                                <tr>
                                    <?php foreach($row as $cell): ?>
                                        <td><?php echo htmlspecialchars($cell); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <?php if(isset($_GET['id']) && !$error): ?>
                    <p>No results found.</p>
                <?php endif; ?>
            <?php endif; ?>
            
             <p style="margin-top: 20px; font-size: 0.9em; color: #8b949e;">
                <i>Tip: Try <b>1 UNION SELECT 1,2,3,4</b> to see if you can inject data.</i>
            </p>
        </div>
        
        <div class="card">
            <h3>Available Song IDs (Reference)</h3>
            <ul>
                <?php while($s = $all_songs->fetch_assoc()): ?>
                    <li>ID: <b><?php echo $s['id']; ?></b> - <?php echo htmlspecialchars($s['title']); ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>
