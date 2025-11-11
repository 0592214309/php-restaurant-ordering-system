<?php
// Simple test script for Phase 1
// This helps verify everything is working correctly

include 'config/db.php';

echo "=== Phase 1 Test Results ===\n\n";

// Test 1: Database Connection
echo "1. Database Connection: ";
if ($conn->connect_error) {
    echo "❌ FAILED - " . $conn->connect_error . "\n";
} else {
    echo "✅ SUCCESS - Connected to 'restaurant' database\n";
}

// Test 2: Categories Table
echo "2. Categories Table: ";
$categories_result = $conn->query("SELECT COUNT(*) as count FROM categories");
if ($categories_result) {
    $row = $categories_result->fetch_assoc();
    echo "✅ SUCCESS - Found " . $row['count'] . " categories\n";
} else {
    echo "❌ FAILED - " . $conn->error . "\n";
}

// Test 3: Menu Items Table
echo "3. Menu Items Table: ";
$items_result = $conn->query("SELECT COUNT(*) as count FROM menu_items");
if ($items_result) {
    $row = $items_result->fetch_assoc();
    echo "✅ SUCCESS - Found " . $row['count'] . " menu items\n";
} else {
    echo "❌ FAILED - " . $conn->error . "\n";
}

// Test 4: Orders Table
echo "4. Orders Table: ";
$orders_result = $conn->query("SELECT COUNT(*) as count FROM orders");
if ($orders_result) {
    $row = $orders_result->fetch_assoc();
    echo "✅ SUCCESS - Orders table ready (current orders: " . $row['count'] . ")\n";
} else {
    echo "❌ FAILED - " . $conn->error . "\n";
}

// Test 5: Sample Menu Items
echo "5. Sample Menu Items:\n";
$sample_result = $conn->query("SELECT m.item_name, c.category_name, m.price FROM menu_items m JOIN categories c ON m.category_id = c.category_id LIMIT 3");
if ($sample_result && $sample_result->num_rows > 0) {
    while ($row = $sample_result->fetch_assoc()) {
        echo "   - " . $row['item_name'] . " (" . $row['category_name'] . ") - GHS " . number_format($row['price'], 2) . "\n";
    }
} else {
    echo "   No items found\n";
}

echo "\n=== All Tests Complete ===\n";
echo "If all tests show ✅ SUCCESS, you're ready to use the app!\n";
?>
