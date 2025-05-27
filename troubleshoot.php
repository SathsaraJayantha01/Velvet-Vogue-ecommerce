<?php
// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Velvet Vogue Database Connection Test</h1>";

// Test database connection
echo "<h2>Testing Database Connection</h2>";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "velvet_vogue";

// Attempt connection without selecting database
echo "<p>Connecting to MySQL server...</p>";
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>Connection failed:</strong> " . $conn->connect_error . "</p>";
    echo "<p>Please check that:</p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Username and password are correct</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green;'><strong>Connected to MySQL server successfully!</strong></p>";
    
    // Check if database exists
    echo "<p>Checking if '$dbname' database exists...</p>";
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'><strong>Database '$dbname' exists!</strong></p>";
        
        // Select the database
        $conn->select_db($dbname);
        
        // Check if users table exists
        echo "<p>Checking if 'users' table exists...</p>";
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'><strong>Table 'users' exists!</strong></p>";
            
            // Check users table structure
            echo "<p>Checking users table structure...</p>";
            $result = $conn->query("DESCRIBE users");
            
            if ($result->num_rows > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Field"] . "</td>";
                    echo "<td>" . $row["Type"] . "</td>";
                    echo "<td>" . $row["Null"] . "</td>";
                    echo "<td>" . $row["Key"] . "</td>";
                    echo "<td>" . $row["Default"] . "</td>";
                    echo "<td>" . $row["Extra"] . "</td>";
                    echo "</tr>";
                }
                
                echo "</table>";
            } else {
                echo "<p style='color: red;'><strong>Error getting table structure!</strong></p>";
            }
            
            // Count users
            echo "<p>Checking number of users in database...</p>";
            $result = $conn->query("SELECT COUNT(*) as count FROM users");
            
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p><strong>Number of users:</strong> " . $row["count"] . "</p>";
            } else {
                echo "<p style='color: red;'><strong>Error counting users:</strong> " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'><strong>Table 'users' does not exist!</strong> Please run the setup-database.php script first.</p>";
        }
    } else {
        echo "<p style='color: red;'><strong>Database '$dbname' does not exist!</strong> Please run the setup-database.php script first.</p>";
    }
}

// Close connection
if (isset($conn)) {
    $conn->close();
}

// PHP Info
echo "<h2>PHP Configuration</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " seconds</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";

// File Permissions
echo "<h2>File Permissions</h2>";
$files = [
    'config.php',
    'register.php',
    'login.php',
    'logout.php',
    'check-auth.php',
    'auth-messages.php',
    'setup-database.php'
];

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>File</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Permissions</th></tr>";

foreach ($files as $file) {
    echo "<tr>";
    echo "<td>" . $file . "</td>";
    
    if (file_exists($file)) {
        echo "<td style='color: green;'>Yes</td>";
        echo "<td>" . (is_readable($file) ? "<span style='color: green;'>Yes</span>" : "<span style='color: red;'>No</span>") . "</td>";
        echo "<td>" . (is_writable($file) ? "<span style='color: green;'>Yes</span>" : "<span style='color: red;'>No</span>") . "</td>";
        echo "<td>" . substr(sprintf('%o', fileperms($file)), -4) . "</td>";
    } else {
        echo "<td style='color: red;'>No</td><td>-</td><td>-</td><td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Testing Register Form Submission
echo "<h2>Testing Form Submission</h2>";
echo "<p>You can test the registration form directly below:</p>";
?>

<form action="register.php" method="post" style="max-width: 500px; margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px;">
    <h3>Test Registration Form</h3>
    
    <div style="margin-bottom: 15px;">
        <label for="test-username" style="display: block; margin-bottom: 5px;">Username:</label>
        <input type="text" id="test-username" name="username" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="test-email" style="display: block; margin-bottom: 5px;">Email:</label>
        <input type="email" id="test-email" name="email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="test-password" style="display: block; margin-bottom: 5px;">Password:</label>
        <input type="password" id="test-password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="test-confirm" style="display: block; margin-bottom: 5px;">Confirm Password:</label>
        <input type="password" id="test-confirm" name="confirm_password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="test-fname" style="display: block; margin-bottom: 5px;">First Name:</label>
        <input type="text" id="test-fname" name="first_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label for="test-lname" style="display: block; margin-bottom: 5px;">Last Name:</label>
        <input type="text" id="test-lname" name="last_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
    </div>
    
    <input type="submit" value="Test Registration" style="background-color: #000; color: #fff; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;">
</form>

<p><a href="setup-database.php" style="display: inline-block; background-color: #000; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Run Database Setup</a>
<a href="index.html" style="display: inline-block; background-color: #333; color: #fff; padding: 10px 15px; text-decoration: none; border-radius: 4px;">Go to Homepage</a></p>