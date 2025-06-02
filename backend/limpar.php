<?php
session_start(); // Inicia a sessão
header("Content-Type: application/json");
include("conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não autenticado."]);
    exit;
}

$usuario_id = $_SESSION["usuario_id"]; // Pega o ID do usuário da sessão

// Usa prepared statement para deletar TODOS os registros APENAS do usuário logado
$sql = "DELETE FROM registros WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação falhou
if ($stmt === false) {
    error_log("Erro ao preparar a consulta de limpeza: " . $conn->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao preparar para limpar registros."]);
    $conn->close();
    exit;
}

// Associa o parâmetro usuario_id (inteiro)
$stmt->bind_param("i", $usuario_id);

// Executa o statement
if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso", "registros_afetados" => $stmt->affected_rows]);
} else {
    error_log("Erro ao executar a limpeza: " . $stmt->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao limpar registros: " . $stmt->error]);
}

// Fecha o statement e a conexão
$stmt->close();
$conn->close();
?>
