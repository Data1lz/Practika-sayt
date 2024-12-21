<?php

// Шляхи до текстових файлів
define('USERS_FILE', 'users.txt');
define('ORDERS_FILE', 'orders.txt');

// Початок сесії для зберігання інформації про авторизованого користувача
session_start();

/**
 * Реєстрація нового користувача
 */
function registerUser($name, $email, $password, $confirmPassword) {
    if ($password !== $confirmPassword) {
        return 'Паролі не співпадають.';
    }
    if (findUserByEmail($email)) {
        return 'Користувач з таким email вже існує.';
    }

    $id = getNextId(USERS_FILE);
    $line = "$id|$name|$email|$password\n";
    file_put_contents(USERS_FILE, $line, FILE_APPEND);
    return 'Реєстрація успішна!';
}

/**
 * Авторизація користувача
 */
function authenticateUser($email, $password) {
    $user = findUserByEmail($email);
    if (!$user) {
        return 'Користувача з таким email не знайдено.';
    }
    if ($user['Password'] !== $password) {
        return 'Невірний пароль.';
    }
    $_SESSION['user_id'] = $user['ID'];
    $_SESSION['user_name'] = $user['Name'];
    return 'Вхід успішний!';
}

/**
 * Додавання нового замовлення
 */
function addOrder($userId, $description, $date) {
    $id = getNextId(ORDERS_FILE);
    $line = "$id|$userId|$description|$date\n";
    file_put_contents(ORDERS_FILE, $line, FILE_APPEND);
    return 'Замовлення успішно додано!';
}

/**
 * Пошук замовлень за ID користувача
 */
function getOrdersByUserId($userId) {
    if (!file_exists(ORDERS_FILE)) return [];

    $lines = file(ORDERS_FILE);
    $orders = [];
    foreach ($lines as $line) {
        list($orderId, $orderUserId, $description, $date) = explode('|', trim($line));
        if ((int)$orderUserId === $userId) {
            $orders[] = [
                'OrderID' => $orderId,
                'Description' => $description,
                'Date' => $date
            ];
        }
    }
    return $orders;
}

/**
 * Отримання наступного ID для запису
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

/**
 * Пошук користувача за email
 */
function findUserByEmail($email) {
    if (!file_exists(USERS_FILE)) return null;

    $lines = file(USERS_FILE);
    foreach ($lines as $line) {
        list($id, $name, $userEmail, $password) = explode('|', trim($line));
        if ($userEmail === $email) {
            return [
                'ID' => $id,
                'Name' => $name,
                'Email' => $userEmail,
                'Password' => $password
            ];
        }
    }
    return null;
}

// Обробка POST-запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'register') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];
            echo registerUser($name, $email, $password, $confirmPassword);
        } elseif ($_POST['action'] === 'login') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            echo authenticateUser($email, $password);
        } elseif ($_POST['action'] === 'addOrder') {
            if (!isset($_SESSION['user_id'])) {
                echo 'Ви повинні увійти в систему для створення замовлення.';
                exit;
            }
            $description = $_POST['description'];
            $date = date('Y-m-d');
            $userId = $_SESSION['user_id'];
            echo addOrder($userId, $description, $date);
        }
    }
}

?>
