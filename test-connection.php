<?php
/**
 * Quick Connection Test
 */

echo "🔍 Testing Database Connection...\n\n";

// Test 1: Database Config
echo "1️⃣ Loading database config...\n";
require_once 'backend/config/database.php';

if ($GLOBALS['db']) {
    echo "✅ Database connected successfully!\n";
    echo "   Host: " . DB_HOST . "\n";
    echo "   Port: " . DB_PORT . "\n";
    echo "   Database: " . DB_NAME . "\n\n";
} else {
    echo "❌ Failed to connect to database\n";
    echo "   Check credentials in backend/config/database.php\n\n";
    exit;
}

// Test 2: Check Table
echo "2️⃣ Checking coffeeshop table...\n";
$result = $GLOBALS['db']->query("SELECT COUNT(*) as total FROM coffeeshops");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Table exists with " . $row['total'] . " records\n\n";
} else {
    echo "❌ Table not found or error: " . $GLOBALS['db']->error . "\n\n";
}

// Test 3: API Test
echo "3️⃣ Testing API endpoint...\n";
echo "   URL: http://localhost:8080/CoffeeshopCimahi/backend/api/coffeeshops.php\n";
echo "   Try this URL in your browser to test\n\n";

// Test 4: Summary
echo "✅ Connection test complete!\n";
echo "Next steps:\n";
echo "1. Open http://localhost:8080/CoffeeshopCimahi/api-test.html in browser\n";
echo "2. Click 'Run All Tests' to verify API endpoints\n";
echo "3. Check browser console (F12) for any errors\n";

?>
