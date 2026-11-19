<?php
session_start();
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/menu.php';

$sql = "SELECT m.quantidade, m.datetime_movimentacao, p.nome AS produto, u.nome AS usuario
        FROM movimentacao m
        JOIN produto p ON p.id_produto = m.id_produto
        LEFT JOIN `user` u ON u.id_user = m.id_user
        WHERE m.tipo = 'SAIDA'
        ORDER BY m.id_movimentacao DESC";
$saidas = $pdo->query($sql)->fetchAll();
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Saídas de Produtos</h1>
                <p class="text-sm text-gray-500">Produtos que tiveram saída do estoque</p>
            </div>
            <a href="movimentacao_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Nova Movimentação
            </a>
        </div>

        <div class="p-6 flex-1">

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Produto</th>
                            <th class="px-4 py-3 text-center">Quantidade</th>
                            <th class="px-4 py-3 text-center">Data</th>
                            <th class="px-4 py-3 text-center">Usuário</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php if (!$saidas): ?>
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Nenhum produto teve saída!</td></tr>
                    <?php endif; ?>

                    <?php foreach ($saidas as $s): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($s["produto"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $s["quantidade"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= date("d/m/Y H:i", strtotime($s["datetime_movimentacao"])) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= htmlspecialchars($s["usuario"] ?? "-") ?></td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= count($saidas) ?> saídas</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
