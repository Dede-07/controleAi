<?php
session_start(); // Inicia a sessão
header("Content-Type: application/json");
include("conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    echo json_encode([]); // Retorna array vazio se não estiver logado ou pode retornar erro
    exit;
}

$usuario_id = $_SESSION["usuario_id"]; // Pega o ID do usuário da sessão

// Usa prepared statement para buscar apenas os registros do usuário logado
$sql = "SELECT id, data, horario, valor FROM registros WHERE usuario_id = ? ORDER BY data DESC, id DESC";
$stmt = $conn->prepare($sql);

// Verifica se a preparação falhou
if ($stmt === false) {
    error_log("Erro ao preparar a consulta: " . $conn->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao buscar registros."]);
    $conn->close();
    exit;
}

// Associa o parâmetro usuario_id
$stmt->bind_param("i", $usuario_id);

// Executa o statement
if (!$stmt->execute()) {
    error_log("Erro ao executar a consulta: " . $stmt->error); // Log do erro
    echo json_encode(["status" => "erro", "mensagem" => "Erro ao buscar registros."]);
    $stmt->close();
    $conn->close();
    exit;
}

// Obtém os resultados
$result = $stmt->get_result();
$registros = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $registros[] = $row;
    }
}

// Retorna os registros em formato JSON
echo json_encode($registros);

// Fecha o statement e a conexão
$stmt->close();
$conn->close();
?>
