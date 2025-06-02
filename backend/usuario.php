<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    "nome" => $_SESSION["usuario_nome"] ?? "Usuário"
]);