<?php
session_start();
require_once "../config/conexao.php";


$sucesso = "";
$erro = "";

// Excluir movimentação
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["excluir"])) {

    $id = filter_input(INPUT_POST, "excluir", FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        $erro = "Movimentação inválida!";
    } else {

        $st = $pdo->prepare("DELETE FROM movimentacao WHERE id = ?");
        $st->execute([$id]);

        if ($st->rowCount() > 0) {
            $sucesso = "Movimentação removida com sucesso!";
        } else {
            $erro = "Não foi possível encontrar a movimentação!";
        }
    }
}

// Mensagem após entrada ou saída
if (isset($_GET["salvo"])) {

    if ($_GET["salvo"] === "saida") {
        $sucesso = "Saída executada com sucesso!";
    } elseif ($_GET["salvo"] === "entrada") {
        $sucesso = "Entrada executada com sucesso!";
    }
}

// Busca as movimentações
$sql = "SELECT
            m.*,
            p.nome AS produto,
            u.nome AS usuario
        FROM movimentacao m
        INNER JOIN produto p ON p.id = m.id_produto
        LEFT JOIN `user` u ON u.id = m.id_user
        ORDER BY m.id DESC";

$movimentacoes = $pdo->query($sql)->fetchAll();

require_once "../includes/menu.php";
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
                <span><?= htmlspecialchars($sucesso) ?></span>
                <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                <span class="font-bold text-base">!</span>
                <span><?= htmlspecialchars($erro) ?></span>
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

                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                                Nenhuma movimentação encontrada!
                            </td>
                        </tr>

                    <?php endif; ?>

                    <?php foreach ($movimentacoes as $m): ?>

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-gray-700 font-semibold">
                                <?= htmlspecialchars($m["produto"]) ?>
                            </td>

                            <td class="px-4 py-3 text-center">

                                <?php if ($m["tipo"] === "ENTRADA"): ?>

                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded">
                                        Entrada
                                    </span>

                                <?php else: ?>

                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded">
                                        Saída
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                <?= (int) $m["quantidade"] ?>
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                <?= date("d/m/Y H:i", strtotime($m["datetime_movimentacao"])) ?>
                            </td>

                            <td class="px-4 py-3 text-center text-gray-600">
                                <?= htmlspecialchars($m["usuario"] ?? "-") ?>
                            </td>

                            <td class="px-4 py-3 text-center">

                                <div class="flex items-center justify-center gap-1.5">

                                    <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta movimentação?')">

                                        <input
                                            type="hidden"
                                            name="excluir"
                                            value="<?= (int) $m["id"] ?>">

                                        <button
                                            type="submit"
                                            class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition">
                                            Excluir
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                <p class="text-xs text-gray-400">
                    Exibindo <?= count($movimentacoes) ?> movimentações
                </p>
            </div>

        </div>

    </div>

</main>

</body>

</html>