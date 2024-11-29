<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início da Avaliação</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .inicio-container {
            text-align: center;
        }
        #comecar-btn, #admin-btn {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            display: inline-block;
        }
        #comecar-btn:hover {
            background-color: #45a049;
        }
        #admin-btn {
        display: inline-block !important;
        visibility: visible !important;
        }
        #admin-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="inicio-container">
        <h1>Bem-vindo à Avaliação de Serviços</h1>
        <p>Por favor, clique no botão abaixo para iniciar sua avaliação ou acesse a tela do administrador.</p>
        
        <a href="index.php">
            <button id="comecar-btn">Começar Avaliação</button>
        </a>
        
        <a href="admin.php">
            <button id="admin-btn">Tela do Administrador</button>
        </a>
    </div>
</body>
</html>
