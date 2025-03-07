<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: #f4f4f9;
      color: #333;
    }

    .login-container {
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h1 {
      margin-bottom: 1rem;
      color: #333;
      font-size: 2rem;
    }

    .login-container .logo {
      margin-bottom: 1.5rem;
    }

    .login-container .logo img {
      max-width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .login-container input {
      width: 100%;
      padding: 0.75rem;
      margin: 0.5rem 0;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .login-container input:focus {
      border-color: #6a11cb;
      outline: none;
    }

    .login-container button {
      width: 100%;
      padding: 0.75rem;
      margin: 1rem 0;
      background-color: #6a11cb;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-container button:hover {
      background-color: #2575fc;
    }

    .login-container .message {
      margin-top: 1rem;
      padding: 0.75rem;
      border-radius: 5px;
      font-size: 0.9rem;
      text-align: center;
    }

    .login-container .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .login-container .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 1rem;
      }

      .login-container h1 {
        font-size: 1.5rem;
      }

      .login-container input,
      .login-container button {
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h1>Iniciar Sesión</h1>
    <div class="logo">
      <img src="/public/img/login-home.jpg" alt="Logo">
    </div>

    <form action="index.php?action=login" method="post">
      <input type="email" name="email" placeholder="Correo electrónico" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Iniciar Sesión</button>
    </form>
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="message <?php echo htmlspecialchars($_SESSION['message_type']); ?>">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
      </div>
      <?php
      unset($_SESSION['message']);
      unset($_SESSION['message_type']);
      ?>
    <?php endif; ?>
  </div>
</body>

</html>