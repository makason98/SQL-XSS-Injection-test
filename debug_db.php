<?php
$tests = [
    ['host' => '127.0.0.1', 'port' => 3306],
    ['host' => '127.0.0.1', 'port' => 8081],
    ['host' => '127.0.0.1', 'port' => 33060],
    ['host' => 'localhost', 'port' => 3306] // Will use socket if port is standard? Actually mysqli treats 'localhost' as socket always unless forced.
];

$user = 'root';
$pass = 'root'; 

echo "Starting Database Connection Tests...\n";
echo "User: $user\nPass: $pass\n\n";

foreach ($tests as $test) {
    $host = $test['host'];
    $port = $test['port'];
    
    echo "Testing $host:$port ... ";
    
    try {
        $conn = new mysqli($host, $user, $pass, '', $port);
        if ($conn->connect_error) {
            echo "FAILED: " . $conn->connect_error . "\n";
        } else {
            echo "SUCCESS! Connected to MySQL " . $conn->server_info . "\n";
            $conn->close();
        }
    } catch (Exception $e) {
        echo "EXCEPTION: " . $e->getMessage() . "\n";
    }
}
?>
