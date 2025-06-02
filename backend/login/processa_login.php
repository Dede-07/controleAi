<?php
session_start();
$_SESSION['usuario_nome'] = $usuario['nome'];
include '../conexao.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    header('Location: login.php?erro=2');
    exit;
}

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: ../../index.php');
        exit;
    } else {
        header('Location: login.php?erro=1'); // Senha incorreta
        exit;
    }
} else {
    header('Location: login.php?erro=1'); // Usuário não encontrado
    exit;
}
?>
