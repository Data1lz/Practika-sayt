<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo 'Ви повинні увійти в систему для перегляду замовлень.';
    exit;
}

define('ORDERS_FILE', 'orders.txt');
$userId = $_SESSION['user_id'];

if (file_exists(ORDERS_FILE)) {
    $lines = file(ORDERS_FILE);
    echo '<h1>Ваші замовлення:</h1><ul>';
    foreach ($lines as $line) {
        list($orderId, $orderUserId, $type, $details, $date) = explode('|', trim($line));
        if ((int)$orderUserId === $userId) {
            echo "<li>[$date] $type: $details</li>";
        }
    }
    echo '</ul>';
} else {
    echo 'Замовлень не знайдено.';
}
?>
