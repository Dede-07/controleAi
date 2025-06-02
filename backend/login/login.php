<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../../index.php');
    exit;
}

$erro = isset($_GET['erro']) ? $_GET['erro'] : null;
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Controle AI</title>
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

        h1 {
            font-family: 'Rosario', sans-serif;
        }

        .container-login {
            display: flex;
            width: 100vw;
            height: 100vh;
        }
        /* Lado esquerdo */
        .left-side {
            background-color: #2f4858;
            width: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .left-side img {
            width: 500px;
            max-width: 80%;
        }
        /* Lado direito */
        .right-side {
            width: 60%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: -10px 0 30px rgba(0,0,0,0.1);
        }
        .login-box {
            width: 350px;
            max-width: 90%;
            text-align: center;
        }
        .login-box img {
            width: 100px;
            margin-bottom: 20px;
        }
        .login-box input {
            width: 100%;
            padding: 10px 15px;
            margin: 10px 0;
            border: 2px solid #000;
            border-radius: 25px;
            outline: none;
        }
        .login-box input::placeholder {
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
        .btn-login {
            background-color: #2f4858;
            color: white;
        }
        .btn-cadastrar {
            background-color: transparent;
            border: 2px solid #2f4858;
            color: #2f4858;
            transition: 0.2s ease-in-out;
        }
        .btn-cadastrar:hover {
            background-color: #2f4858;
            color: white;
        }
        .btn-login:hover {
            opacity: 0.9;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .left-side {
                display: none;
            }
            .right-side {
                width: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="container-login">
    <!-- Lado esquerdo (some no mobile) -->
    <div class="left-side">
        <img src="../../LogoCA.png" alt="Logo">
    </div>


    <!-- Lado direito (formulário) -->
    <div class="right-side">
    <div class="login-box">
         <img src="../../LogoCA.png" alt="Logo">

         <h1 style="color:#2f4858, font-weigth: bold;">Login</h1>

        <?php if($erro): ?>
        <div class="alert alert-danger mb-3" role="alert">
            <?php
                if ($erro == 1) {
                    echo "Email ou senha incorretos.";
                } elseif ($erro == 2) {
                    echo "Por favor, preencha todos os campos.";
                }
            ?>
        </div>
        <?php endif; ?>

        <form action="processa_login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <div class="buttons">
                <button type="submit" class="btn-login">Login <i class="bi bi-box-arrow-in-right"></i></button>
                <a href="register.php" class="btn-cadastrar">Cadastrar <i class="bi bi-person-add"></i></a>
            </div>
        </form>
    </div>
</div>

</div>

</body>
</html>
