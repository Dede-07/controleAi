<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Controle AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" href="../../LogoCA.png" type="image/png">

    <style>
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css");
        @import url('https://fonts.googleapis.com/css2?family=Rosario:ital,wght@0,300..700;1,300..700&display=swap');

        * {
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            margin: 0;
            background-color: #f5f7fa;
        }

        h1{
            font-family: 'Rosario', sans-serif;
        }

        .container-cadastro {
            display: flex;
            width: 100vw;
            height: 100vh;
        }
        /* Lado esquerdo (formulário aqui no cadastro) */
        .left-side {
            width: 60%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 10px 0 30px rgba(0,0,0,0.1);
        }
        .cadastro-box {
            width: 350px;
            max-width: 90%;
            text-align: center;
        }
        .cadastro-box img {
            width: 100px;
            margin-bottom: 20px;
        }
        .cadastro-box input {
            width: 100%;
            padding: 10px 15px;
            margin: 10px 0;
            border: 2px solid #000;
            border-radius: 25px;
            outline: none;
        }
        .cadastro-box input::placeholder {
            color: #aaa;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .buttons button, .buttons a {
            padding: 10px 25px;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cadastrar {
            background-color: #2f4858;
            color: white;
        }
        .btn-login {
            background-color: transparent;
            border: 2px solid #2f4858;
            color: #2f4858;
            transition: 0.2s ease-in-out;
        }
        .btn-login:hover {
            background-color: #2f4858;
            color: white;
        }
        .btn-cadastrar:hover {
            opacity: 0.9;
        }

        /* Lado direito (logo no cadastro) */
        .right-side {
            background-color: #2f4858;
            width: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .right-side img {
            width: 500px;
            max-width: 80%;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .right-side {
                display: none;
            }
            .left-side {
                width: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="container-cadastro">
    <!-- Lado esquerdo (Formulário no cadastro) -->
    <div class="left-side">
        <div class="cadastro-box">
            <img src="../../LogoCA.png" alt="Logo">
            <h1 style="color:#2f4858, font-weigth: bold;">Criar Conta</h1>
            <form action="processa_register.php" method="POST">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <div class="buttons">
                    <button type="submit" class="btn-cadastrar">Cadastrar <i class="bi bi-person-add"></i></button>
                    <a href="login.php" class="btn-login">Login <i class="bi bi-box-arrow-in-right"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lado direito (Logo no cadastro) -->
    <div class="right-side">
        <img src="../../LogoCA.png" alt="Logo">
    </div>
</div>

</body>
</html>
