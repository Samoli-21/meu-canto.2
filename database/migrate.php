<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Apenas linha de comandos.'); }
require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$pdo = Database::pdo();

// Migração não destrutiva: nunca executa DROP TABLE.
$sqlFile = __DIR__ . '/migrations/001_schema.sql';
$sql = (string) file_get_contents($sqlFile);
$lines = array_filter(explode("\n", $sql), static fn ($line) => !str_starts_with(ltrim($line), '--'));
$statements = preg_split('/;\s*(?:\r?\n|$)/', implode("\n", $lines)) ?: [];
foreach ($statements as $statement) {
    $statement = trim($statement);
    if ($statement === '' || str_starts_with($statement, '--')) continue;
    $pdo->exec($statement);
}

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$expected = ['usuarios', 'profissionais_saude', 'conteudos_site'];
$missing = array_values(array_diff($expected, $tables));
if ($missing) {
    throw new RuntimeException('Não foram criadas todas as entidades: ' . implode(', ', $missing));
}
fwrite(STDOUT, "Migração concluída. Entidades de negócio: usuarios, profissionais_saude, conteudos_site.\n");
