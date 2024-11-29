<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');  // Certifique-se de que essa é a senha correta

// Função para obter a conexão com o banco de dados
function getDBConnection() {
    try {
        // Tentando criar a conexão PDO
        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Exibe a mensagem de erro e interrompe a execução
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
        exit;
    }
}

// Inicia a sessão
session_start();

// Verifica se o usuário já está logado, se estiver, redireciona para o painel
if (isset($_SESSION['usuario'])) {
    header("Location: http://localhost/hospital/public/admin.php"); // Redireciona para o painel administrativo
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // Obtém a conexão com o banco de dados
    $pdo = getDBConnection();

    // Prepara a consulta SQL para verificar o login e senha
    $stmt = $pdo->prepare("SELECT * FROM usuarios_administrativos WHERE login = :login AND senha = :senha");
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    // Verifica se o usuário foi encontrado
    if ($stmt->rowCount() > 0) {
        // Inicia a sessão e redireciona para o painel administrativo
        $_SESSION['usuario'] = $login;
        header("Location: http://localhost/hospital/public/admin.php"); // Redireciona para o painel administrativo
        exit;
    } else {
        // Caso o login ou senha estejam errados
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
