<?php
// database.php

class Database {
    private $usersFile = 'database/users.txt';
    private $ordersFile = 'database/orders.txt';

    public function __construct() {
        // Створюємо директорію для бази даних якщо її немає
        if (!file_exists('database')) {
            mkdir('database', 0777, true);
        }
        
        // Створюємо файли якщо вони не існують
        if (!file_exists($this->usersFile)) {
            file_put_contents($this->usersFile, '');
        }
        if (!file_exists($this->ordersFile)) {
            file_put_contents($this->ordersFile, '');
        }
    }

    // Функція для реєстрації користувача
    public function registerUser($name, $email, $password) {
        $users = $this->getAllUsers();
        
        // Перевіряємо чи email вже існує
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return false;
            }
        }
        
        // Хешуємо пароль
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Створюємо новий запис
        $newUser = "$name|$email|$hashedPassword\n";
        
        // Додаємо користувача до файлу
        file_put_contents($this->usersFile, $newUser, FILE_APPEND);
        
        return true;
    }

    // Функція для авторизації
    public function loginUser($email, $password) {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }

    // Отримати всіх користувачів
    private function getAllUsers() {
        $users = [];
        $lines = file($this->usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines) {
            foreach ($lines as $line) {
                list($name, $email, $password) = explode('|', $line);
                $users[] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ];
            }
        }
        
        return $users;
    }

    // Зберегти нове замовлення
    public function saveOrder($userEmail, $orderData) {
        $orderTime = date('Y-m-d H:i:s');
        $orderString = "$orderTime|$userEmail|" . serialize($orderData) . "\n";
        file_put_contents($this->ordersFile, $orderString, FILE_APPEND);
        return true;
    }

    // Отримати замовлення користувача
    public function getUserOrders($userEmail) {
        $orders = [];
        $lines = file($this->ordersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines) {
            foreach ($lines as $line) {
                list($time, $email, $data) = explode('|', $line, 3);
                if ($email === $userEmail) {
                    $orders[] = [
                        'time' => $time,
                        'data' => unserialize($data)
                    ];
                }
            }
        }
        
        return $orders;
    }
}
?>
