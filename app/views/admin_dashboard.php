<?php

require_once __DIR__ . '/../../helpers/ValidSession.php';
require_once __DIR__ . '/../controllers/UserController.php';

Helper::validSession('admin', '/public/index.php');

$users = UserController::usersWithAccount();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de Administrador</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 300px;
        text-align: center;
    }

    .modal-content input {
        width: 80%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .modal-content button {
        margin: 5px;
        width: 40%;

    }

    .modal-content .confirm {
        background: #28a745;
    }

    .modal-content .cancel {
        background: #dc3545;
    }

    body {
        font-family: Arial, sans-serif;
        background: #f4f4f9;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
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

    .card.form-card {
        width: 30%;
        min-width: 250px;
    }

    .card.users-card {
        width: 70%;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-align: left;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    input,
    select {
        width: 50%;
        margin: 0 10px 10px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    input {
        width: 90%;
    }

    button {
        width: 100%;
        padding: 10px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: #218838;
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

        .card.form-card,
        .card.users-card {
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
        <h1>Panel de Administración</h1>

        <!-- Mensajes de sesión -->
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="message <?= htmlspecialchars($_SESSION['message_type']) ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="cards">
            <div class="card form-card">
                <h2>Crear Nuevo Usuario</h2>
                <form action="index.php?action=create_user" method="post">
                    <label for="tipo_usuario">Tipo de usuario:</label>
                    <select id="tipo_usuario" name="tipo_usuario" required>
                        <option value="">Selecciona un rol</option>
                        <option value="customer">Cliente</option>
                        <option value="admin">Administrador</option>
                    </select>

                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="email">Correo:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="saldo">Saldo Inicial:</label>
                    <input type="number" id="saldo" name="saldo" step="1" required>

                    <button type="submit">Crear Usuario</button>
                </form>
            </div>

            <!-- Card de Lista de Usuarios (70%) -->
            <div class="card users-card">
                <h2>Lista de Usuarios</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Saldo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user->id); ?></td>
                                    <td><?= htmlspecialchars($user->name); ?></td>
                                    <td><?= htmlspecialchars($user->email); ?></td>
                                    <td><?= $user->account ? number_format($user->account->balance, 0) : '0'; ?> COP</td>
                                    <td>
                                        <button class="edit-btn" onclick="openModal(<?= $user->id; ?>, <?= $user->account ? $user->account->balance : 0; ?>)">Editar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Saldo -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h1>Editar Saldo</h1>
            <h5>Edición de saldo para cuentas de los clientes.</h5>
            <input type="number" id="editBalance" step="1" required>
            <button class="confirm" onclick="updateBalance()">Confirmar</button>
            <button class="cancel" onclick="closeModal()">Cancelar</button>
        </div>
    </div>
    <script>
        let currentUserId = null;

        function openModal(userId, currentBalance) {
            currentUserId = userId;
            document.getElementById('editBalance').value = currentBalance;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function updateBalance() {
            const newBalance = document.getElementById('editBalance').value;
            if (newBalance && currentUserId) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = 'index.php?action=update_balance';
                const userIdInput = document.createElement('input');
                userIdInput.type = 'hidden';
                userIdInput.name = 'user_id';
                userIdInput.value = currentUserId;

                const balanceInput = document.createElement('input');
                balanceInput.type = 'hidden';
                balanceInput.name = 'new_balance';
                balanceInput.value = newBalance;

                form.appendChild(userIdInput);
                form.appendChild(balanceInput);
                document.body.appendChild(form);
                form.submit();
            }
            closeModal();
        }
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        };
        document.getElementById("tipo_usuario").addEventListener("change", function() {
            let form = document.getElementById("user-form");
            if (this.value) {
                form.classList.remove("hidden");
            } else {
                form.classList.add("hidden");
            }
        });
    </script>
</body>

</html>