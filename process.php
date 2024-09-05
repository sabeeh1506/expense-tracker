<?php
// Database configuration
$host = 'localhost';
$dbname = 'expense_tracker';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $description = $_POST['description'];
    $amount = (float)$_POST['amount'];
    $date = $_POST['date'];
    $type = $_POST['type'];

    $stmt = $pdo->prepare("INSERT INTO expenses (description, amount, date, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$description, $amount, $date, $type]);

    echo json_encode(['success' => true, 'transaction' => [
        'id' => $pdo->lastInsertId(),
        'description' => $description,
        'amount' => $amount,
        'date' => $date,
        'type' => $type
    ]]);
} elseif ($action === 'delete') {
    $id = (int)$_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);
} elseif ($action === 'get_totals') {
    $stmt = $pdo->query("SELECT type, SUM(amount) AS total FROM expenses GROUP BY type");
    $totals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $incomeTotal = 0;
    $expenseTotal = 0;

    foreach ($totals as $total) {
        if ($total['type'] === 'income') {
            $incomeTotal = (float)$total['total'];
        } elseif ($total['type'] === 'expense') {
            $expenseTotal = (float)$total['total'];
        }
    }

    echo json_encode(['incomeTotal' => $incomeTotal, 'expenseTotal' => $expenseTotal]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
