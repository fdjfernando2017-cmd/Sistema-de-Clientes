
<?php

$host = '127.0.0.1';
$porta = 3306;
$banco = 'sistema_clientes';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Erro ao conectar ao banco de dados. Confira o MySQL e o arquivo conexao.php.');
}
