
<?php
require_once 'conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'cadastrar') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = 'Informe um nome e um e-mail válido.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO clientes (nome, email, telefone)
                 VALUES (:nome, :email, :telefone)'
            );

            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone
            ]);

            header('Location: index.php?sucesso=1');
            exit;
        }
    }

    if ($acao === 'excluir') {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id && $id > 0) {
            $stmt = $pdo->prepare(
                'DELETE FROM clientes WHERE id = :id'
            );
            $stmt->execute(['id' => $id]);
        }

        header('Location: index.php');
        exit;
    }
}

if (isset($_GET['sucesso'])) {
    $mensagem = 'Cliente cadastrado com sucesso!';
}

$stmt = $pdo->query(
    'SELECT id, nome, email, telefone
     FROM clientes
     ORDER BY id DESC'
);
$clientes = $stmt->fetchAll();

function escapar($valor) {
    return htmlspecialchars(
        (string) ($valor ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Cadastro de Clientes</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 35px auto;
            padding: 0 20px;
            background: #f3f5f7;
            color: #263238;
        }

        h1, h2 { color: #20364b; }

        .painel {
            background: white;
            padding: 22px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }

        button {
            padding: 10px 16px;
            margin-top: 15px;
            cursor: pointer;
            border: 0;
            border-radius: 4px;
            background: #2457a7;
            color: white;
        }

        .excluir { background: #b42318; }

        .mensagem {
            background: #e2f5e9;
            padding: 12px;
            margin-bottom: 20px;
        }

        .tabela { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th { background: #e9edf2; }

        td form { margin: 0; }
    </style>
</head>
<body>

    <h1>Sistema de Cadastro de Clientes</h1>
    <p>Gerenciamento de clientes</p>

    <?php if ($mensagem !== ''): ?>
        <p class="mensagem"><?= escapar($mensagem) ?></p>
    <?php endif; ?>

    <section class="painel">
        <h2>Novo cliente</h2>

        <form method="POST">
            <input type="hidden" name="acao" value="cadastrar">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome"
                   maxlength="100" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email"
                   maxlength="150" required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone"
                   maxlength="20">

            <button type="submit">Cadastrar cliente</button>
        </form>
    </section>

    <section class="painel">
        <h2>Clientes cadastrados (<?= count($clientes) ?>)</h2>

        <?php if (empty($clientes)): ?>
            <p>Nenhum cliente cadastrado ainda.</p>
        <?php else: ?>
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= escapar($cliente['id']) ?></td>
                                <td><?= escapar($cliente['nome']) ?></td>
                                <td><?= escapar($cliente['email']) ?></td>
                                <td><?= escapar($cliente['telefone']) ?></td>
                                <td>
                                    <form method="POST"
                                          onsubmit="return confirm('Deseja excluir este cliente?');">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id"
                                               value="<?= escapar($cliente['id']) ?>">
                                        <button type="submit" class="excluir">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</body>
</html>
