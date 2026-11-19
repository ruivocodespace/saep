<?php
session_start();
require_once __DIR__ . '/conexao.php';

$erro = "";

$id_produto = (int) ($_GET["id_produto"] ?? 0);
$tipo       = "ENTRADA";
$quantidade = "";
$id_user    = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_produto = (int) ($_POST["id_produto"] ?? 0);
    $tipo       = $_POST["tipo"] ?? "";
    $quantidade = (int) ($_POST["quantidade"] ?? 0);
    $id_user    = (int) ($_POST["id_user"] ?? 0);

    $st = $pdo->prepare("SELECT quantidade_estoque FROM produto WHERE id_produto = ?");
    $st->execute([$id_produto]);
    $produto = $st->fetch();

    $st = $pdo->prepare("SELECT id_user FROM `user` WHERE id_user = ?");
    $st->execute([$id_user]);
    $user = $st->fetch();

    if (!$id_produto || !$quantidade || !$id_user || $tipo === "") {
        $erro = "Preencha todos os campos!";
    } elseif ($tipo !== "ENTRADA" && $tipo !== "SAIDA") {
        $erro = "Tipo não aceito!";
    } elseif ($quantidade <= 0) {
        $erro = "Quantidade inválida!";
    } elseif (!$produto) {
        $erro = "Não foi possível encontrar o produto!";
    } elseif (!$user) {
        $erro = "Usuário não encontrado!";
    } elseif ($tipo === "SAIDA" && $produto["quantidade_estoque"] < $quantidade) {
        $erro = "Estoque insuficiente! Disponível: " . $produto["quantidade_estoque"] . ".";
    }

    if ($erro === "") {
        $st = $pdo->prepare("INSERT INTO movimentacao (tipo, quantidade, id_produto, datetime_movimentacao, id_user) VALUES (?, ?, ?, NOW(), ?)");
        $st->execute([$tipo, $quantidade, $id_produto, $id_user]);

        // ENTRADA soma no estoque, SAIDA subtrai
        $delta = $tipo === "ENTRADA" ? $quantidade : -$quantidade;
        $st = $pdo->prepare("UPDATE produto SET quantidade_estoque = quantidade_estoque + ? WHERE id_produto = ?");
        $st->execute([$delta, $id_produto]);

        header("Location: movimentacoes.php?salvo=" . strtolower($tipo));
        exit;
    }
}

$produtos = $pdo->query("SELECT id_produto, nome, quantidade_estoque FROM produto ORDER BY nome")->fetchAll();
$usuarios = $pdo->query("SELECT id_user, nome FROM `user` ORDER BY nome")->fetchAll();

require_once __DIR__ . '/includes/menu.php';
?>
    <main class="flex-1 flex flex-col">
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="movimentacoes.php" class="hover:text-senai-blue">Movimentações</a> ›
                <span class="text-gray-700 font-semibold">Nova Movimentação</span>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800">Alterar Estoque</h1>
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
                        <label class="form-label">Produto *</label>
                        <select name="id_produto" class="form-input" required>
                            <option value="">Selecione o produto</option>
                            <?php foreach ($produtos as $p): ?>
                                <option value="<?= $p["id_produto"] ?>" <?= $p["id_produto"] == $id_produto ? "selected" : "" ?>><?= htmlspecialchars($p["nome"]) ?> (estoque: <?= $p["quantidade_estoque"] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Tipo *</label>
                        <select name="tipo" class="form-input" required>
                            <option value="ENTRADA" <?= $tipo == "ENTRADA" ? "selected" : "" ?>>Entrada</option>
                            <option value="SAIDA" <?= $tipo == "SAIDA" ? "selected" : "" ?>>Saída</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Quantidade *</label>
                        <input type="number" name="quantidade" class="form-input" required min="1" step="1" placeholder="Ex: 10" value="<?= htmlspecialchars($quantidade) ?>">
                    </div>
                    <div class="mb-6">
                        <label class="form-label">Usuário responsável *</label>
                        <select name="id_user" class="form-input" required>
                            <option value="">Selecione o usuário</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u["id_user"] ?>" <?= $u["id_user"] == $id_user ? "selected" : "" ?>><?= htmlspecialchars($u["nome"]) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-senai-blue text-white font-bold px-5 py-2.5 rounded-lg text-sm hover:bg-senai-blue-dark transition">💾 Salvar</button>
                        <a href="movimentacoes.php" class="bg-gray-100 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200 transition">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
