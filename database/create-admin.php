<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Apenas linha de comandos.');
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

// Se já existir uma conta administrativa, não bloqueia o arranque por variáveis
// de bootstrap vazias/alteradas e nunca substitui a palavra-passe existente.
$exists = (int) Database::query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'admin'")->fetchColumn();
if ($exists > 0) {
    fwrite(STDOUT, "Já existe um administrador; nenhuma alteração foi feita.\n");
    exit(0);
}

$name = trim((string) env('ADMIN_NAME', 'Administrador Meu Canto'));
$email = strtolower(trim((string) env('ADMIN_EMAIL', '')));
$password = (string) env('ADMIN_PASSWORD', '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "ADMIN_EMAIL inválido. Define um endereço de email válido no Render.\n");
    exit(1);
}
if (strlen($password) < 12) {
    fwrite(STDERR, "ADMIN_PASSWORD deve conter pelo menos 12 caracteres.\n");
    exit(1);
}

Database::query(
    "INSERT INTO usuarios (nome, email, password_hash, tipo, estado) VALUES (?, ?, ?, 'admin', 'ativo')",
    [$name !== '' ? $name : 'Administrador Meu Canto', $email, password_hash($password, PASSWORD_DEFAULT)]
);

fwrite(STDOUT, "Administrador inicial criado.\n");
