<?php
require_once "../config/conexao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        senai: {
                            red: '#C0392B',
                            blue: '#34679A',
                            'blue-dark': '#2C5A85',
                            orange: '#E67E22',
                            green: '#27AE60'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .form-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            outline: none;
            transition: border .15s, box-shadow .15s;
        }

        .form-input:focus {
            border-color: #34679A;
            box-shadow: 0 0 0 3px rgba(52, 103, 154, .15);
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">

        <div class="bg-senai-blue px-6 py-5 text-white">
            <h1 class="text-xl font-bold tracking-tight">Cadastro de Usuário</h1>
            <p class="text-xs text-slate-200 mt-1">Preencha os dados abaixo para criar seu acesso</p>
        </div>

        <form action="salvar.php" method="POST" class="p-6 space-y-4">
            <div>
                <label for="nome" class="form-label">Nome</label>
                <input
                    type="text"
                    name="nome"
                    id="nome"
                    placeholder="Digite seu nome completo"
                    class="form-input"
                    required>
            </div>

            <div>
                <label for="email" class="form-label">E-mail</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="seu@email.com"
                    class="form-input"
                    required>
            </div>

            <div>
                <label for="senha" class="form-label">Senha</label>
                <input
                    type="password"
                    name="senha"
                    id="senha"
                    placeholder="••••••••"
                    class="form-input"
                    required>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full bg-senai-blue hover:bg-senai-blue-dark text-white font-semibold py-2.5 px-4 rounded-lg transition-colors shadow-sm">
                    Cadastrar
                </button>
            </div>
            <div class="pt-2">
                <a href="../index.php" class="text-center block w-full bg-senai-blue hover:bg-senai-blue-dark text-white font-semibold py-2.5 px-4 rounded-lg transition-colors shadow-sm">Voltar</a>
            </div>

        </form>
    </div>
</body>

</html>