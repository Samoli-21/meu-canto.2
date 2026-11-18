<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $c = Config::get('database');
        if (empty($c['host']) || empty($c['name']) || empty($c['user'])) {
            throw new RuntimeException('Configuração da base de dados incompleta.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $c['host'], $c['port'], $c['name'], $c['charset']
        );
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 10,
        ];

        if (!empty($c['ssl'])) {
            $ca = (string) ($c['ssl_ca'] ?? '');
            if ($ca === '' || !is_file($ca)) {
                throw new RuntimeException('Certificado CA do TiDB não encontrado. Define DB_SSL_CA.');
            }
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
            }
        }

        try {
            self::$pdo = new PDO($dsn, (string) $c['user'], (string) $c['pass'], $options);
            self::$pdo->exec("SET time_zone = '+01:00'");
        } catch (PDOException $e) {
            error_log('TiDB connection failed: ' . $e->getMessage());
            throw new RuntimeException('Não foi possível ligar à base de dados. Confirma o host, firewall e TLS.');
        }

        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
