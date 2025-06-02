<?php
session_start(); // Inicia a sessão
header("Content-Type: application/json");
include('conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não autenticado."]);
    exit; // Interrompe a execução se não estiver logado
}

$usuario_id = $_SESSION['usuario_id']; // Pega o ID do usuário da sessão
$data = $_POST['data'];
$horario = $_POST['horario'];
$valor = $_POST['valor'];

// Usa prepared statement para prevenir SQL injection
$sql = "INSERT INTO registros (data, horario, valor, usuario_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Verifica se a preparação falhou
if ($stmt === false) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao preparar a consulta: " . $conn->error]);
    $conn->close();
    exit;
}

// Assumindo que 'valor' pode ser decimal e 'usuario_id' é inteiro. Ajuste os tipos se necessário (s=string, i=integer, d=double)
$stmt->bind_param("ssdi", $data, $horario, $valor, $usuario_id);

// Executa o statement
if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso"]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao adicionar registro: " . $stmt->error]);
}

// Fecha o statement e a conexão
$stmt->close();
$conn->close();
?>
