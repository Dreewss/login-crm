<?php
session_start();

// Configuração do banco de dados
$host = "localhost";
$user = "root";
$pass = "";
$db = "crm";

// Criando conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificando conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Inicializa variável de erro
$error = "";

// Função para realizar login
function login($email, $password)
{
    global $conn, $error;

    // Preparando a consulta para evitar SQL Injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificando se o usuário existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificando a senha usando password_verify
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Senha incorreta!";
        }
    } else {
        $error = "Usuário não encontrado!";
    }

    $stmt->close();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validando entrada
    if (!empty($email) && !empty($password)) {
        login($email, $password);
    } else {
        $error = "Preencha todos os campos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color:rgb(17, 17, 17);
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-container">  
    <h2>Login</h2>  
    <form method="post">  
        <label for="email">Email:</label>  
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>  
        
        <label for="password">Senha:</label>  
        <input type="password" id="password" name="password" required>  
        
        <button type="submit" name="login">Entrar</button>  

        <?php if (!empty($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </form>  
</div>  

</body>
</html>
