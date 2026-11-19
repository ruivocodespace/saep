<?php
// ============================================
// Arquivo: conexao.php
// Função: Conectar o PHP ao banco de dados MySQL usando PDO
// ============================================

// Dados de conexão com o banco
$host  = 'localhost';
$banco = 'empresa';
$user  = 'root';
$senha = '';                     // no WampServer o root vem sem senha

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $user,
        $senha,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    exit('Falha na conexão: ' . $e->getMessage());
}
