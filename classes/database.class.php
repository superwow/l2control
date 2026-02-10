<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );

final class Database
{
    /** @var array<string, PDO> */
    private static array $pool = [];

    public static function conn(?int $serverId = null): PDO
    {
        $key = $serverId === null ? 'login' : 'game:' . $serverId;
        if (isset(self::$pool[$key])) {
            return self::$pool[$key];
        }

        if ($serverId === null) {
            $cfg = CONFIG::g()->login_server;
        } else {
            $cfg = CONFIG::g()->select_game_server($serverId);
            if ($cfg === null) {
                throw new RuntimeException('Game server #' . $serverId . ' is not configured');
            }
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            $cfg['hostname'],
            $cfg['database']
        );

        $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        self::$pool[$key] = $pdo;
        return $pdo;
    }

    public static function query(string $sql, array $params = [], ?int $serverId = null): PDOStatement
    {
        $stmt = self::conn($serverId)->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = [], ?int $serverId = null): ?array
    {
        $stmt = self::query($sql, $params, $serverId);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function fetchValue(string $sql, array $params = [], ?int $serverId = null, int $column = 0): mixed
    {
        $stmt = self::query($sql, $params, $serverId);
        $value = $stmt->fetchColumn($column);
        return $value === false ? null : $value;
    }

    public static function execute(string $sql, array $params = [], ?int $serverId = null): int
    {
        $stmt = self::query($sql, $params, $serverId);
        return $stmt->rowCount();
    }

    public static function fetchAll(string $sql, array $params = [], ?int $serverId = null): array
    {
        $stmt = self::query($sql, $params, $serverId);
        return $stmt->fetchAll();
    }

    public static function reset(): void
    {
        self::$pool = [];
    }
}

?>
