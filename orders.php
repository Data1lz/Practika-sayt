<?php

// Шлях до текстового файлу для замовлень
define('ORDERS_FILE', 'orders.txt');

/**
 * Додавання нового замовлення до файлу
 * @param int $userId - ID користувача
 * @param string $type - Тип замовлення (товар або послуга)
 * @param string $details - Деталі замовлення (опис)
 * @param string $date - Дата замовлення
 * @return string - Повідомлення про успіх
 */
function addOrder($userId, $type, $details, $date) {
    $id = getNextId(ORDERS_FILE);
    $line = "$id|$userId|$type|$details|$date\n";
    file_put_contents(ORDERS_FILE, $line, FILE_APPEND);
    return 'Замовлення успішно додано!';
}

/**
 * Отримання наступного ID для нового замовлення
 */
function getNextId($file) {
    if (!file_exists($file) || filesize($file) === 0) {
        return 1;
    }
    $lines = file($file);
    $lastLine = array_pop($lines);
    $lastId = (int)explode('|', $lastLine)[0];
    return $lastId + 1;
}

// Обробка POST-запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        echo 'Ви повинні увійти в систему для створення замовлення.';
        exit;
    }

    // Отримуємо дані форми
    $type = $_POST['type']; // товар або послуга
    $details = $_POST['details']; // деталі замовлення
    $date = date('Y-m-d'); // поточна дата
    $userId = $_SESSION['user_id']; // ID авторизованого користувача

    echo addOrder($userId, $type, $details, $date);
}

?>
