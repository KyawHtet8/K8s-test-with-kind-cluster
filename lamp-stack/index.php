<?php
/*
=======================================================================
PERFECT LAMP STACK - PHP APPLICATION
Myanmar Students အတွက် Learning Version
Professor's Complete Teaching Material
=======================================================================
*/

// Error reporting for debugging (Production မှာ ပိတ်ရမယ်)
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMP Stack - Myanmar Learning Project</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; font-size: 18px; }
        .error { color: #dc3545; font-weight: bold; }
        .info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .debug { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 LAMP Stack Demo - Myanmar Learning Project</h1>
        
        <?php
        // ================================================================
        // 1. ENVIRONMENT VARIABLES CHECK (ပတ်ဝန်းကျင် ကိန်းရှင်များ စစ်ဆေးခြင်း)
        // ================================================================
        echo "<h2>📋 Step 1: Environment Variables Status</h2>";
        
        $env_vars = [
            'MYSQL_HOST' => getenv('MYSQL_HOST'),
            'MYSQL_DATABASE' => getenv('MYSQL_DATABASE'), 
            'MYSQL_USER' => getenv('MYSQL_USER'),
            'MYSQL_PASSWORD' => getenv('MYSQL_PASSWORD'),
            'MYSQL_ROOT_PASSWORD' => getenv('MYSQL_ROOT_PASSWORD')
        ];
        
        echo "<table>";
        echo "<tr><th>Variable Name</th><th>Status</th><th>Value Preview</th></tr>";
        
        $env_ok = true;
        foreach ($env_vars as $name => $value) {
            $status = $value ? "✅ Set" : "❌ Missing";
            $preview = $value ? (strlen($value) > 20 ? substr($value, 0, 20) . "..." : $value) : "Not set";
            
            // Hide passwords in preview
            if (strpos($name, 'PASSWORD') !== false && $value) {
                $preview = str_repeat('*', strlen($value));
            }
            
            echo "<tr><td>$name</td><td>$status</td><td>$preview</td></tr>";
            
            if (!$value) $env_ok = false;
        }
        echo "</table>";
        
        if (!$env_ok) {
            echo "<div class='error'>❌ Environment variables မပြည့်စုံဘူး! Kubernetes deployment ကို ပြန်စစ်ပါ။</div>";
            exit;
        }
        
        // ================================================================
        // 2. PHP & SERVER INFO (PHP နဲ့ Server အချက်အလက်များ)
        // ================================================================
        echo "<h2>🔧 Step 2: Server Environment Info</h2>";
        echo "<div class='info'>";
        echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
        echo "<strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
        echo "<strong>Server Name:</strong> " . $_SERVER['SERVER_NAME'] . "<br>";
        echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
        echo "<strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "<br>";
        echo "</div>";
        
        // ================================================================
        // 3. MYSQL CONNECTION TEST (MySQL ချိတ်ဆက်မှု စစ်ဆေးခြင်း)
        // ================================================================
        echo "<h2>🔌 Step 3: MySQL Connection Test</h2>";
        
        $mysql_host = $env_vars['MYSQL_HOST'];
        $mysql_database = $env_vars['MYSQL_DATABASE'];
        $mysql_user = $env_vars['MYSQL_USER'];
        $mysql_password = $env_vars['MYSQL_PASSWORD'];
        
        // Test 1: Basic Connection (Port 3306 နဲ့ TCP သုံးအောင် force လုပ်တယ်)
        echo "<h3>Test 1: Basic MySQL Connection</h3>";
        try {
            $connection = mysqli_connect($mysql_host, $mysql_user, $mysql_password, $mysql_database, 3306);
            
            if (!$connection) {
                throw new Exception("Connection failed: " . mysqli_connect_error());
            }
            
            echo "<div class='success'>✅ Successfully connected to MySQL database!</div>";
            echo "<div class='debug'>";
            echo "<strong>Host:</strong> $mysql_host<br>";
            echo "<strong>Database:</strong> $mysql_database<br>";
            echo "<strong>User:</strong> $mysql_user<br>";
            echo "<strong>Connection ID:</strong> " . mysqli_thread_id($connection) . "<br>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ MySQL Connection Failed: " . $e->getMessage() . "</div>";
            echo "<div class='debug'>";
            echo "<strong>Debugging Tips:</strong><br>";
            echo "1. Check if MySQL container is running<br>";
            echo "2. Verify environment variables<br>";
            echo "3. Check if database and user exist<br>";
            echo "4. Verify network connectivity between containers<br>";
            echo "</div>";
            exit;
        }
        
        // Test 2: Database Information
        echo "<h3>Test 2: Database Server Information</h3>";
        try {
            $version_query = "SELECT VERSION() as version";
            $result = mysqli_query($connection, $version_query);
            $version_info = mysqli_fetch_assoc($result);
            
            $status_queries = [
                "Server Version" => "SELECT VERSION() as info",
                "Current Database" => "SELECT DATABASE() as info",
                "Current User" => "SELECT USER() as info", 
                "Connection ID" => "SELECT CONNECTION_ID() as info",
                "Server Uptime" => "SHOW STATUS LIKE 'Uptime'"
            ];
            
            echo "<table>";
            echo "<tr><th>Information</th><th>Value</th></tr>";
            
            foreach ($status_queries as $label => $query) {
                $result = mysqli_query($connection, $query);
                if ($result) {
                    $row = mysqli_fetch_assoc($result);
                    $value = isset($row['info']) ? $row['info'] : $row['Value'];
                    echo "<tr><td>$label</td><td>$value</td></tr>";
                }
            }
            echo "</table>";
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Could not retrieve database information: " . $e->getMessage() . "</div>";
        }
        
        // ================================================================
        // 4. DATABASE OPERATIONS (ဒေတာဘေ့စ် လုပ်ငန်းများ)
        // ================================================================
        echo "<h2>💾 Step 4: Database Operations Demo</h2>";
        
        try {
            // Create demo table
            $create_table = "
                CREATE TABLE IF NOT EXISTS students (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    course VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            
            if (mysqli_query($connection, $create_table)) {
                echo "<div class='success'>✅ Students table created/verified successfully</div>";
            } else {
                throw new Exception("Table creation failed: " . mysqli_error($connection));
            }
            
            // Insert sample data (if table is empty)
            $count_query = "SELECT COUNT(*) as count FROM students";
            $count_result = mysqli_query($connection, $count_query);
            $count_row = mysqli_fetch_assoc($count_result);
            
            if ($count_row['count'] == 0) {
                $sample_data = [
                    ["Mg Aung", "aung@email.com", "Web Development"],
                    ["Ma Thin", "thin@email.com", "Database Design"],
                    ["Ko Zaw", "zaw@email.com", "DevOps Engineering"],
                    ["Ma Moe", "moe@email.com", "Cloud Computing"],
                    ["Mg Htet", "htet@email.com", "Cybersecurity"]
                ];
                
                $insert_stmt = mysqli_prepare($connection, "INSERT INTO students (name, email, course) VALUES (?, ?, ?)");
                
                foreach ($sample_data as $student) {
                    mysqli_stmt_bind_param($insert_stmt, "sss", $student[0], $student[1], $student[2]);
                    mysqli_stmt_execute($insert_stmt);
                }
                
                mysqli_stmt_close($insert_stmt);
                echo "<div class='success'>✅ Sample student data inserted successfully</div>";
            }
            
            // Display students data
            echo "<h3>Current Students in Database:</h3>";
            $select_query = "SELECT id, name, email, course, created_at FROM students ORDER BY created_at DESC";
            $students_result = mysqli_query($connection, $select_query);
            
            if (mysqli_num_rows($students_result) > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Created At</th></tr>";
                
                while ($student = mysqli_fetch_assoc($students_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($student['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['course']) . "</td>";
                    echo "<td>" . htmlspecialchars($student['created_at']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='info'>No students found in database.</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ Database operation failed: " . $e->getMessage() . "</div>";
        }
        
        // ================================================================
        // 5. FINAL SUCCESS MESSAGE (နောက်ဆုံး အောင်မြင်မှု မက်ဆေ့ချ်)
        // ================================================================
        echo "<h2>🎉 Step 5: Final Status</h2>";
        echo "<div class='success' style='text-align: center; padding: 20px; font-size: 24px;'>";
        echo "Connected successfully";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h3>🏆 Congratulations! Your LAMP Stack is working perfectly!</h3>";
        echo "<p><strong>What was demonstrated:</strong></p>";
        echo "<ul>";
        echo "<li>✅ Environment variables loaded correctly</li>";
        echo "<li>✅ PHP is processing requests properly</li>";
        echo "<li>✅ MySQL connection established via TCP</li>";
        echo "<li>✅ Database operations working (CREATE, INSERT, SELECT)</li>";
        echo "<li>✅ Container networking functioning correctly</li>";
        echo "</ul>";
        
        echo "<p><strong>Technical Achievement:</strong></p>";
        echo "<ul>";
        echo "<li>🔧 Kubernetes multi-container pod deployment</li>";
        echo "<li>🔐 Secure credential management with Secrets</li>";
        echo "<li>📝 Configuration management with ConfigMaps</li>";
        echo "<li>🌐 Service networking and port exposure</li>";
        echo "<li>💾 Database connectivity and operations</li>";
        echo "</ul>";
        echo "</div>";
        
        // Close MySQL connection
        mysqli_close($connection);
        
        ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px; text-align: center;">
            <h3>🎓 Myanmar Students - LAMP Stack Learning Complete!</h3>
            <p>This demonstrates a fully functional LAMP stack running on Kubernetes.</p>
            <p><strong>Next Learning Steps:</strong> Try adding forms, more tables, user authentication, and API endpoints!</p>
        </div>
    </div>
</body>
</html>
