<?php
// order.php
session_start();
require_once 'database.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Перевіряємо чи користувач авторизований
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Необхідно увійти в систему']);
        exit;
    }
    
    $userEmail = $_SESSION['user']['email'];
    $orderData = [
        'device' => $_POST['device'] ?? '',
        'glassType' => $_POST['glassType'] ?? '',
        'quantity' => $_POST['quantity'] ?? 1,
        'services' => $_POST['services'] ?? [],
        'totalPrice' => $_POST['totalPrice'] ?? 0
    ];
    
    if ($db->saveOrder($userEmail, $orderData)) {
        echo json_encode(['success' => true, 'message' => 'Замовлення збережено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка при збереженні замовлення']);
    }
}

// Отримання замовлень користувача
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['user'])) {
    $userEmail = $_SESSION['user']['email'];
    $orders = $db->getUserOrders($userEmail);
    echo json_encode(['success' => true, 'orders' => $orders]);
}
?>
