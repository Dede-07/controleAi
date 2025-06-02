<?php
session_start(); // Inicia a sessão
header("Content-Type: application/json");
include("conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["status" => "erro", "mensagem" => "Usuário não autenticado."]);
    exit;
}

// Verifica se o ID do registro foi enviado
if (!isset($_POST["id"])) {
    echo json_encode(["status" => "erro", "mensagem" => "ID do registro não fornecido."]);
    exit;
}

$usuario_id = $_SESSION["usuario_id"]; // Pega o ID do usuário da sessão
$id = $_POST["id"]; // Pega o ID do registro a ser deletado

// Usa prepared statement para deletar o registro específico do usuário logado
$sql = "DELETE FROM registros WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação falhou
if ($stmt === false) {
    error_log("Erro ao preparar a consulta de deleção: " . $conn->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao preparar para deletar registro."]);
    $conn->close();
    exit;
}

// Associa os parâmetros id e usuario_id (ambos inteiros)
$stmt->bind_param("ii", $id, $usuario_id);

// Executa o statement
if ($stmt->execute()) {
    // Verifica se alguma linha foi afetada (se o registro existia e pertencia ao usuário)
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "sucesso"]);
    } else {
        echo json_encode(["status" => "erro", "mensagem" => "Registro não encontrado ou não pertence a este usuário."]);
    }
} else {
    error_log("Erro ao executar a deleção: " . $stmt->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao deletar registro: " . $stmt->error]);
}

// Fecha o statement e a conexão
$stmt->close();
$conn->close();
?>
