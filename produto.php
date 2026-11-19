<?php
session_start();
require_once __DIR__ . '/conexao.php';

$st = $pdo->prepare("SELECT * FROM produto WHERE id_produto = :id");
$st->execute(["id" => $_GET["id"] ?? 0]);
$produto = $st->fetch();          // false se não encontrou

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="produtos.php" class="hover:text-senai-blue">Produtos</a> ›
                <span class="text-gray-700 font-semibold">Detalhes do Produto</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800"><?= $produto ? htmlspecialchars($produto["nome"]) : "Produto" ?></h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">

            <?php if (!$produto): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    Nenhum produto encontrado!
                </div>
                <a href="produtos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Voltar</a>
            <?php else: ?>
                <?php $valorTotal = round($produto["valor_unitario"] * $produto["quantidade_estoque"], 2); ?>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="mb-4">
                        <p class="form-label">Produto</p>
                        <p class="text-gray-800 font-semibold"><?= htmlspecialchars($produto["nome"]) ?></p>
                    </div>
                    <div class="mb-4">
                        <p class="form-label">Valor unitário</p>
                        <p class="text-gray-800">R$ <?= number_format($produto["valor_unitario"], 2, ",", ".") ?></p>
                    </div>
                    <div class="mb-4">
                        <p class="form-label">Quantidade em estoque</p>
                        <p class="text-gray-800"><?= $produto["quantidade_estoque"] ?></p>
                    </div>
                    <div class="mb-4">
                        <p class="form-label">Valor total em estoque</p>
                        <p class="text-2xl font-extrabold text-gray-800">R$ <?= number_format($valorTotal, 2, ",", ".") ?></p>
                    </div>
                    <div class="mb-6">
                        <p class="form-label">Data de cadastro</p>
                        <p class="text-gray-800"><?= date("d/m/Y", strtotime($produto["data_cadastro"])) ?></p>
                    </div>

                    <div class="flex gap-2">
                        <a href="movimentacao_form.php?id_produto=<?= $produto["id_produto"] ?>" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">🔄 Movimentar estoque</a>
                        <a href="produtos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Voltar</a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
