<?php
// auth.php
session_start();
require_once 'database.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'register':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($db->registerUser($name, $email, $password)) {
                echo json_encode(['success' => true, 'message' => 'Реєстрація успішна']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email вже існує']);
            }
            break;
            
        case 'login':
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $db->loginUser($email, $password);
            if ($user) {
                $_SESSION['user'] = [
                    'name' => $user['name'],
                    'email' => $user['email']
                ];
                echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Неправильний email або пароль']);
            }
            break;
            
        case 'logout':
            session_destroy();
            echo json_encode(['success' => true]);
            break;
    }
}
?>
