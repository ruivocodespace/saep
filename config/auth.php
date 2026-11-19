<?php
session_start();
// Verifica se o usuário está autenticado
function exigirLogin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../usuarios/login.php');
        exit;
    }
}

// Verifica o perfil do usuário
function exigirPerfil($perfil)
{
    exigirLogin();

    if ($_SESSION['usuario_tipo'] !== $perfil) {
        exit('Acesso negado. Você não tem permissão para acessar esta página.');
    }
}

// Verifica se o usuário possui um dos perfis permitidos
function exigirUmDosPerfis($perfis)
{
    exigirLogin();

    if (!in_array($_SESSION['usuario_tipo'], $perfis)) {
        exit('Acesso negado.');
    }
}
