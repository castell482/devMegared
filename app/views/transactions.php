<?php

require_once __DIR__ . '/../../helpers/ValidSession.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/TransactionController.php';

Helper::validSession('customer', '/public/index.php');

$user = UserController::userWithAccount();

$transaction = new TransactionController;
$transacciones = $transaction->history();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacciones</title>
    <link rel="stylesheet" href="styles.css">
</head>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f9;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        min-height: 100vh;
    }

    .container {
        text-align: center;
        max-width: 80%;
        width: 100%;
        padding: 20px;
        margin: 0 auto;
    }

    .cards {
        display: flex;
        gap: 20px;
        justify-content: space-between;
        align-items: flex-start;
    }

    .card.transferencia {
        width: 30%;
        min-width: 250px;
    }

    .card.historial {
        width: 70%;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-align: left;
    }

    /* Mensajes de éxito y error */
    .message {
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
        text-align: center;
        font-weight: bold;
        font-size: 1rem;
        width: 80%;
        margin: 10px auto;
        transition: opacity 0.5s ease-out;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        font-weight: bold;
    }

    .form-group input {
        width: 80%;
        padding: 10px;
        border-radius: 5px;
        font-size: 1rem;
        background: rgb(239, 239, 239);
        margin-top: 10px;
    }

    button {
        width: 50%;
        padding: 10px;
        background: #6a11cb;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #2575fc;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background: #333;
        color: white;
    }

    tr:nth-child(even) {
        background: #f9f9f9;
    }

    @media (max-width: 900px) {
        .cards {
            flex-direction: column;
            align-items: center;
        }

        .card.transferencia,
        .card.historial {
            width: 100%;
        }
    }

    .logout {
        position: absolute;
        top: 10px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 10px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .logout:hover {
        background: #c82333;
    }
</style>

<body>

    <a href="index.php?action=logout" class="logout">Cerrar Sesión</a>

    <div class="container">
        <h1>Bienvenido, <?= htmlspecialchars($user->email) ?></h1>

        <div class="cards">
            <div class="card transferencia">
                <h1>Transferir Dinero</h1>
                <p class="saldo">Saldo disponible: <?= number_format($user->account->balance, 2) ?> COP</p>

                <form action="/public/index.php?action=transfer_account" method="post">
                    <div class="form-group">
                        <label for="monto">Monto a transferir:</label>
                        <input type="number" id="monto" name="monto" step="0.01" min="0.01" max="<?= $user->account->balance ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="cuenta_destino">Número de cuenta destino:</label>
                        <input type="number" id="cuenta_destino" name="cuenta_destino" required>
                    </div>
                    <button type="submit">Transferir</button>
                </form>

                <?php if (!empty($_SESSION['message'])): ?>
                    <div id="notification" class="message <?= htmlspecialchars($_SESSION['message_type']) ?>">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                    </div>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>
            </div>

            <div class="card historial">
                <h1>Historial de Transacciones</h1>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Origen → Destino</th>
                                <th>Cuenta</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transacciones as $t): ?>
                                <tr>
                                    <td><?= htmlspecialchars($t->transaction_type) ?></td>
                                    <td><?= htmlspecialchars($t->from_user) . " → " . htmlspecialchars($t->to_user) ?></td>
                                    <td><?= htmlspecialchars($t->account_id) . " → " . htmlspecialchars($t->related_account_id) ?></td>
                                    <td><?= htmlspecialchars($t->created_at) ?></td>
                                    <td><?= number_format($t->amount, 2) ?> COP</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const notification = document.getElementById("notification");
            if (notification) {
                notification.style.opacity = "0";
                setTimeout(() => notification.remove(), 500);
            }
        }, 5000);
    </script>

</body>

</html>
