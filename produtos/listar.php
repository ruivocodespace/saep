<?php
session_start();
require_once "../config/conexao.php";

// Recebe os filtros
$busca = trim($_GET['busca'] ?? '');

// Configuração da paginação
$por_pagina = 5;
$pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);

if (!$pagina || $pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $por_pagina;

// Monta as condições da pesquisa
$condicoes = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = "p.nome LIKE :busca";
    $parametros[':busca'] = "%$busca%";
}

$where = '';

if (count($condicoes) > 0) {
    $where = ' WHERE ' . implode(' AND ', $condicoes);
}

// Conta o total de produtos encontrados
$sql = "SELECT COUNT(*)
        FROM produto p
        $where";

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);

$total_produtos = $stmt->fetchColumn();

// Calcula o total de páginas
$total_paginas = ceil($total_produtos / $por_pagina);

// Busca os produtos da página atual
$sql = "SELECT
            p.id,
            p.nome,
            p.valor_unitario,
            p.quantidade_estoque
        FROM produto p
        $where
        ORDER BY p.nome ASC
        LIMIT :inicio, :por_pagina";

$stmt = $pdo->prepare($sql);

// Vincula os parâmetros dos filtros
foreach ($parametros as $chave => $valor) {
    $stmt->bindValue($chave, $valor);
}

// Vincula os parâmetros da paginação como inteiros
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);

$stmt->execute();

$produtos = $stmt->fetchAll();

require_once "../includes/menu.php";

?>

<main class="flex-1 flex flex-col">

    <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Produtos</h1>
            <p class="text-sm text-gray-500">Cadastre, consulte e remova os produtos do estoque</p>
        </div>
        <a href="cadastro.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
            + Novo Produto
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

        <!-- Pesquisa e filtros -->
        <form method="GET" class="bg-white rounded-xl shadow-sm p-4 mb-5">

            <div class="flex flex-col md:flex-row gap-3">

                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Pesquisar produto
                    </label>
                    <input
                        type="text"
                        name="busca"
                        value="<?= htmlspecialchars($busca) ?>"
                        placeholder="Digite o nome do produto..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 outline-none focus:ring-2 focus:ring-senai-blue">
                </div>

                <div class="flex items-end gap-2">
                    <button
                        type="submit"
                        class="bg-senai-blue text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-senai-blue-dark transition">
                        Pesquisar
                    </button>

                    <a
                        href="listar.php"
                        class="bg-gray-200 text-gray-700 font-semibold px-5 py-2.5 rounded-lg hover:bg-gray-300 transition">
                        Limpar
                    </a>
                </div>

            </div>

        </form>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-senai-blue text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Produto</th>
                        <th class="px-4 py-3 text-center">Valor unitário</th>
                        <th class="px-4 py-3 text-center">Quantidade</th>
                        <th class="px-4 py-3 text-center">Valor total</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    <?php if (count($produtos) > 0): ?>

                        <?php foreach ($produtos as $p): ?>
                            <?php $valorTotal = round($p["valor_unitario"] * $p["quantidade_estoque"], 2); ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($p["nome"]) ?></td>
                                <td class="px-4 py-3 text-center text-gray-600">R$ <?= number_format($p["valor_unitario"], 2, ",", ".") ?></td>
                                <td class="px-4 py-3 text-center text-gray-600"><?= $p["quantidade_estoque"] ?></td>
                                <td class="px-4 py-3 text-center text-gray-600 font-semibold">R$ <?= number_format($valorTotal, 2, ",", ".") ?></td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="produto.php?id=<?= $p["id"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver produto">🔍 Ver</a>
                                        <a href="movimentacao_form.php?id=<?= $p["id"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Movimentar estoque">🔄 Movimentar</a>
                                        <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $p["id"] ?>">Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Nenhum produto encontrado.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

            <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                <p class="text-xs text-gray-400">
                    Exibindo <?= count($produtos) ?> de <?= $total_produtos ?> produtos
                </p>
            </div>
        </div>

        <!-- Paginação -->
        <?php if ($total_paginas > 1): ?>

            <div class="flex justify-center items-center gap-2 mt-5">

                <?php if ($pagina > 1): ?>
                    <a
                        href="?pagina=<?= $pagina - 1 ?>&busca=<?= urlencode($busca) ?>"
                        class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">
                        Anterior
                    </a>
                <?php endif; ?>

                <span class="text-sm text-gray-600">
                    Página <?= $pagina ?> de <?= $total_paginas ?>
                </span>

                <?php if ($pagina < $total_paginas): ?>
                    <a
                        href="?pagina=<?= $pagina + 1 ?>&busca=<?= urlencode($busca) ?>"
                        class="px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">
                        Próxima
                    </a>
                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>
</main>

</body>

</html>