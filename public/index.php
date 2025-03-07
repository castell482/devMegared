<?php

require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/AccountController.php';
require_once __DIR__ . '/../app/controllers/TransactionController.php';

$action = $_GET['action'] ?? 'login';
$userController = new UserController;
$accountController = new AccountController;
$transactionController = new TransactionController;

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            try {
                $loginResult = $userController->login($email, $password);
                
                if ($loginResult === true) {
                    $_SESSION['message'] = "Inicio de sesión exitoso.";
                    $_SESSION['message_type'] = 'success';
    
                    if ($userController->isRole('admin')) {
                        header("Location: /public/index.php?action=admin_dashboard");
                    } else {
                        header("Location: /public/index.php?action=transactions");
                    }
                    exit();
                } else {
                    $_SESSION['message'] = "Usuario o contraseña incorrectos.";
                    $_SESSION['message_type'] = 'error';
                }
            } catch (Exception $e) {
                $_SESSION['message'] = "Usuario o contraseña incorrectos.";
                $_SESSION['message_type'] = 'error';
            }
            
            include '../app/views/login.php';
        } else {
            include '../app/views/login.php';
        }
        break;
    
    
    case 'admin_dashboard':
        if ($userController->isRole('admin')) {
            include '../app/views/admin_dashboard.php';
        } else {
            header("Location: /public/index.php");
        }
        break;
    case 'create_user':
        if ($userController->isRole('admin') && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $saldo = $_POST['saldo'];
            $tipo_usuario = $_POST['tipo_usuario'];

            try {
                $userController->register($tipo_usuario, $nombre, $email, $password, $saldo);

                $_SESSION['message'] = "Usuario creado exitosamente.";
                $_SESSION['message_type'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['message'] = "Error al crear el usuario: " . $e->getMessage();
                $_SESSION['message_type'] = 'error';
            }

            header("Location: /public/index.php?action=admin_dashboard");
        } else {
            header("Location: /public/index.php");
        }
        break;
    case 'update_balance':
        if ($userController->isRole('admin') && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'];
            $new_balance = $_POST['new_balance'];

            $account_id = $accountController::account($user_id)->id;
            $transactionController->deposit($account_id, $new_balance);

            $_SESSION['message'] = "Saldo actualizado exitosamente.";
            $_SESSION['message_type'] = 'success';
            header("Location: /public/index.php?action=admin_dashboard");
        } else {
            header("Location: /public/index.php");
        }
        break;
    case 'logout':
        $userController->logout();
        header("Location: /public/index.php");
        break;
    default:
        include '../app/views/login.php';
        break;
    case 'transfer_account':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fromAccountId = $accountController::account()->id;
            $toAccountId = $_POST['cuenta_destino'];
            $amount = $_POST['monto'];

            try {
                $transactionController->transfer($fromAccountId, $toAccountId, $amount);
                $_SESSION['message'] = "Transferencia realizada exitosamente.";
                $_SESSION['message_type'] = 'success';
            } catch (Exception $e) {
                $_SESSION['message'] = "Error al transferir: " . $e->getMessage();
                $_SESSION['message_type'] = 'error';
            }

            header("Location: /public/index.php?action=transactions");
            exit();
        }
        break;

    case 'transactions':
        if (isset($userController->user()->id)) {
            include '../app/views/transactions.php';
        } else {
            header("Location: /public/index.php");
        }
        break;
}
