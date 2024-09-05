<?php
// Database configuration
$host = 'localhost'; // Change if needed
$dbname = 'expense_tracker'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password (change if needed)

try {
    // Create a new PDO instance and connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all expenses and income from the database
$stmt = $pdo->query("SELECT * FROM expenses ORDER BY date DESC");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$incomeTotal = array_sum(array_column(array_filter($transactions, function($t) { return $t['type'] === 'income'; }), 'amount'));
$expenseTotal = array_sum(array_column(array_filter($transactions, function($t) { return $t['type'] === 'expense'; }), 'amount'));
?>