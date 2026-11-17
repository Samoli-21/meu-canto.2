<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Apenas linha de comandos.'); }
require dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Database;

$name = trim((string) env('ADMIN_NAME', 'Administrador Meu Canto'));
$email = strtolower(trim((string) env('ADMIN_EMAIL', '')));
$password = (string) env('ADMIN_PASSWORD', '');

if ($email === '' || $password === '') {
    fwrite(STDOUT, "ADMIN_EMAIL/ADMIN_PASSWORD não definidos; criação inicial do administrador ignorada.\n");
    exit(0);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    fwrite(STDERR, "ADMIN_EMAIL inválido ou ADMIN_PASSWORD com menos de 12 caracteres.\n");
    exit(1);
}

$exists = (int) Database::query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'admin'")->fetchColumn();
if ($exists > 0) {
    fwrite(STDOUT, "Já existe um administrador; nenhuma alteração foi feita.\n");
    exit(0);
}
Database::query(
    "INSERT INTO usuarios (nome, email, password_hash, tipo, estado) VALUES (?, ?, ?, 'admin', 'ativo')",
    [$name, $email, password_hash($password, PASSWORD_DEFAULT)]
);
fwrite(STDOUT, "Administrador inicial criado.\n");
