<?php
require_once '../config/conexao.php';

// Verifica se o formulário foi enviado por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Método inválido.");
}

// Recebe os dados do formulário
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Valida os campos
if (empty($nome) || empty($email) || empty($senha)) {
    exit("Preencha todos os campos.");
}
// Valida o email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('E-mail inválido.');
}

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Define o perfil padrão (operador)
$tipo = 'operador';

try {

    // Consulta SQL
    $sql = "INSERT INTO user (nome, email, senha, tipo)
            VALUES (:nome, :email, :senha, :tipo)";

    $stmt = $pdo->prepare($sql);

    // Executa a inserção
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':tipo' => $tipo
    ]);

    echo "Usuário cadastrado com sucesso!";
    echo "<br><a href='cadastro.php'>Cadastrar outro usuário</a>";
    echo "<br><a href='../index.php'>Ir para o dashboard</a>";
} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        exit('Este e-mail já está cadastrado.');
    }

    exit('Erro ao cadastrar usuário.');
}
