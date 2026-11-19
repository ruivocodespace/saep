<?php
session_start();
require_once __DIR__ . '/conexao.php';

$erro = "";

$nome       = "";
$quantidade = "";
$valor      = "";
$data       = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome       = trim($_POST["nome"] ?? "");       // ?? evita aviso se o campo não veio
    $quantidade = trim($_POST["quantidade"] ?? "");
    $valor      = trim($_POST["valor"] ?? "");
    $data       = trim($_POST["data"] ?? "");

    if ($nome === "" || $quantidade === "" || $valor === "" || $data === "") {
        $erro = "Preencha todos os campos!";
    } elseif ((int) $quantidade < 0 || (float) $valor < 0) {
        $erro = "Quantidade e valor não podem ser negativos.";
    } else {
        $st = $pdo->prepare("SELECT id_produto FROM produto WHERE nome = ?");
        $st->execute([$nome]);
        if ($st->fetch()) {
            $erro = "Produto já cadastrado!";
        }
    }

    if ($erro === "") {
        $st = $pdo->prepare("INSERT INTO produto (nome, quantidade_estoque, valor_unitario, data_cadastro) VALUES (?, ?, ?, ?)");
        $st->execute([$nome, (int) $quantidade, (float) $valor, $data]);

        header("Location: produtos.php?salvo=1");     // redireciona após gravar
        exit;
    }
}

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="produtos.php" class="hover:text-senai-blue">Produtos</a> ›
                <span class="text-gray-700 font-semibold">Novo Produto</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Novo Produto</h1>
        </div>
        <div class="p-6 flex-1 max-w-xl">

            <?php if (!empty($erro)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?= $erro ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="post">
                    <div class="mb-4">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" class="form-input" required maxlength="100" placeholder="Ex: Detergente" value="<?= htmlspecialchars($nome) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Quantidade em estoque *</label>
                        <input type="number" name="quantidade" class="form-input" required min="0" step="1" placeholder="Ex: 25" value="<?= htmlspecialchars($quantidade) ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Valor unitário (R$) *</label>
                        <input type="number" name="valor" class="form-input" required min="0" step="0.01" placeholder="Ex: 18.90" value="<?= htmlspecialchars($valor) ?>">
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Data de cadastro *</label>
                        <input type="date" name="data" class="form-input" required value="<?= htmlspecialchars($data) ?>">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="produtos.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
