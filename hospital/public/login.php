<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres'); 

function getDBConnection() {
    try {
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        exit;
    }
}

session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: http://localhost/hospital/public/admin.php"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT * FROM usuarios_administrativos WHERE login = :login AND senha = :senha");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['usuario'] = $login;
        header("Location: http://localhost/hospital/public/admin.php"); 
        exit;
    } else {
        $error_message = "Login ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>

<body>

    <div class="login-container">
        <h2>Área de Login</h2>
        
        <?php if (isset($error_message)) { ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php } ?>

        <form id="loginForm" action="login.php" method="POST">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br>

            <button type="submit">Entrar</button>
        </form>
    </div>

</body>

</html>
