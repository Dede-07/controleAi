<?php
include '../conexao.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senha);

if ($stmt->execute()) {
    header('Location: login.php');
} else {
    echo "Erro ao cadastrar: " . $stmt->error;
}
?>
