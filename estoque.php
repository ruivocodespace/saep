<?php
session_start();
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/includes/menu.php';

$produtos = $pdo->query("SELECT * FROM produto ORDER BY quantidade_estoque, nome")->fetchAll();
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Verificar Estoque</h1>
                <p class="text-sm text-gray-500">Quantidade disponível de cada produto</p>
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
                            <th class="px-4 py-3 text-center">Valor unitário</th>
                            <th class="px-4 py-3 text-center">Quantidade</th>
                            <th class="px-4 py-3 text-center">Situação</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php if (!$produtos): ?>
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Nenhum produto encontrado!</td></tr>
                    <?php endif; ?>

                    <?php foreach ($produtos as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($p["nome"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600">R$ <?= number_format($p["valor_unitario"], 2, ",", ".") ?></td>
                            <td class="px-4 py-3 text-center text-gray-800 font-bold"><?= $p["quantidade_estoque"] ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($p["quantidade_estoque"] > 0): ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">Em estoque</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded">Sem estoque</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="movimentacao_form.php?id_produto=<?= $p["id_produto"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Movimentar estoque">🔄 Movimentar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= count($produtos) ?> produtos</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
