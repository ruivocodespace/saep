
<?php
require_once '../config/conexao.php';

session_start();

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Modo inválido.');
}

// Recebe os dados
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Verifica se os campos foram preenchidos
if (empty($email) || empty($senha)) {
    exit('Preencha todos os campos.');
}

try {

    // Busca o usuário pelo e-mail
    $sql = "SELECT * FROM user WHERE email = :email";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);

    $usuario = $stmt->fetch();

    // Verifica se o usuário existe e se a senha está correta
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        exit('E-mail ou senha incorretos.');
    }

    // Gera um novo identificador de sessão
    session_regenerate_id(true);

    // Armazena os dados do usuário na sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];

    // Redireciona para a página inicial
    header('Location: ../index.php');
    exit;
} catch (PDOException $e) {

    exit('Erro ao realizar login.');
}
