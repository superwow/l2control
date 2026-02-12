<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );

final class Database
{
    /** @var array */
    private static $pool = array();

    /** @var array */
    private static $charsetMap = array(
        'utf-8' => 'utf8mb4',
        'utf8' => 'utf8mb4',
        'utf8mb4' => 'utf8mb4',
        'cp1251' => 'cp1251',
        'windows-1251' => 'cp1251',
        'latin1' => 'latin1',
    );

    /**
     * @param int|null $serverId
     * @return PDO
     */
    public static function conn($serverId = null)
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

        $charset = self::resolveCharset((string) CONFIG::g()->core_iso_type);
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $cfg['hostname'],
            $cfg['database'],
            $charset
        );

        $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ));

        self::$pool[$key] = $pdo;
        return $pdo;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int|null $serverId
     * @return PDOStatement
     */
    public static function query($sql, $params = array(), $serverId = null)
    {
        $stmt = self::conn($serverId)->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int|null $serverId
     * @return array|null
     */
    public static function fetchOne($sql, $params = array(), $serverId = null)
    {
        $stmt = self::query($sql, $params, $serverId);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int|null $serverId
     * @param int $column
     * @return mixed
     */
    public static function fetchValue($sql, $params = array(), $serverId = null, $column = 0)
    {
        $stmt = self::query($sql, $params, $serverId);
        $value = $stmt->fetchColumn($column);
        return $value === false ? null : $value;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int|null $serverId
     * @return int
     */
    public static function execute($sql, $params = array(), $serverId = null)
    {
        $stmt = self::query($sql, $params, $serverId);
        return $stmt->rowCount();
    }

    /**
     * @param string $sql
     * @param array $params
     * @param int|null $serverId
     * @return array
     */
    public static function fetchAll($sql, $params = array(), $serverId = null)
    {
        $stmt = self::query($sql, $params, $serverId);
        return $stmt->fetchAll();
    }

    public static function reset()
    {
        self::$pool = array();
    }

    /**
     * @param string $charset
     * @return string
     */
    private static function resolveCharset($charset)
    {
        $normalized = strtolower(trim($charset));
        if ($normalized === '') {
            return 'utf8mb4';
        }

        return isset(self::$charsetMap[$normalized]) ? self::$charsetMap[$normalized] : 'utf8mb4';
    }
}

?>
