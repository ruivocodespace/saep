<?php
session_start();
require_once __DIR__ . '/conexao.php';

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"])) {
    $st = $pdo->prepare("DELETE FROM movimentacao WHERE id_movimentacao = ?");
    $st->execute([$_GET["excluir"]]);
    if ($st->rowCount() > 0) {
        $sucesso = "Movimentação removida com sucesso!";
    } else {
        $erro = "Não foi possível encontrar a movimentação!";
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = $_GET["salvo"] === "saida" ? "Saída executada com sucesso!" : "Entrada executada com sucesso!";
}

require_once __DIR__ . '/includes/menu.php';

$sql = "SELECT m.*, p.nome AS produto, u.nome AS usuario
        FROM movimentacao m
        JOIN produto p ON p.id_produto = m.id_produto
        LEFT JOIN `user` u ON u.id_user = m.id_user
        ORDER BY m.id_movimentacao DESC";
$movimentacoes = $pdo->query($sql)->fetchAll();
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Movimentações</h1>
                <p class="text-sm text-gray-500">Entradas e saídas de produtos do estoque</p>
            </div>
            <a href="movimentacao_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Nova Movimentação
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
                            <th class="px-4 py-3 text-left">Produto</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-center">Quantidade</th>
                            <th class="px-4 py-3 text-center">Data</th>
                            <th class="px-4 py-3 text-center">Usuário</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php if (!$movimentacoes): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Nenhuma movimentação encontrada!</td></tr>
                    <?php endif; ?>

                    <?php foreach ($movimentacoes as $m): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($m["produto"]) ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($m["tipo"] == "ENTRADA"): ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Entrada</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded">Saída</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $m["quantidade"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= date("d/m/Y H:i", strtotime($m["datetime_movimentacao"])) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($m["usuario"] ?? "-") ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $m["id_movimentacao"] ?>">Excluir</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= count($movimentacoes) ?> movimentações</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
