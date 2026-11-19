<?php
session_start();
require_once "../config/conexao.php";

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"])) {
    try {
        $st = $pdo->prepare("DELETE FROM user WHERE id = ?");
        $st->execute([$_GET["excluir"]]);

        if ($st->rowCount() > 0) {
            $sucesso = "Usuário removido com sucesso!";
        } else {
            $erro = "Não foi possível encontrar usuário!";
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1451) {       // código MySQL: registro possui dependentes (FK)
            $erro = "Este não pode ser removido.";
        } else {
            throw $e;
        }
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Usuário cadastrado com sucesso!";
}

require_once "../includes/menu.php";

$usuarios = $pdo->query("SELECT * FROM user ORDER BY nome")->fetchAll();
$totalUser = "SELECT COUNT(id) AS total FROM user;"
?>
<main class="flex-1 flex flex-col">

    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Usuários</h1>
            <p class="text-sm text-gray-500">Cadastre, consulte e remova os usuários do sistema</p>
        </div>
        <a href="cadastro.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
            + Novo Usuário
        </a>
    </div>

    <div class="p-6 flex-1">

        <?php if (!empty($sucesso)): ?>
            <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">✓</span>
                <span><?= $sucesso ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">!</span>
                <span><?= $erro ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-700 text-lg leading-none">×</button>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-senai-blue text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-center">Email</th>
                        <th class="px-4 py-3 text-center">Perfil</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    <?php foreach ($usuarios as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($p["nome"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $p["email"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold">Perfil <?= $p["tipo"] ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="atualizar.php?id=<?= $p["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Atualizar Usuário">🔄 Editar</a>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $p["user"] ?>">Excluir</a>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

            <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                <p class="text-xs text-gray-400">Exibindo <?= count($usuarios) ?> usuarios</p>
            </div>
        </div>

    </div>
</main>

</body>

</html>