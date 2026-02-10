# PHP 8 + PDO modernization plan (TODO by PR)

## Важные предположения

1. Цель — **сохранить текущее поведение ACP/L2 панели**, без переписывания бизнес-логики в один большой PR.
2. Миграция делается поэтапно: сначала совместимость с PHP 8.2+, затем безопасная БД-абстракция на PDO, затем обновление библиотек.
3. Деплой без Composer сейчас, но итоговая целевая архитектура — с Composer + автозагрузка + CI.

---

## Быстрый аудит текущего состояния

### 1) Legacy template engine
- В проекте зашит **Smarty 2.6.31-dev**, который сильно устарел и проблемен на новых версиях PHP.
- Файл: `libs/Smarty.class.php`.

### 2) Смешанный слой БД (mysqli + legacy mysql_)
- `classes/mysql.class.php` уже использует `mysqli_*`, но по коду всё ещё есть вызовы удалённых `mysql_*`:
  - `mysql_real_escape_string` в `classes/world.class.php`.
  - `mysql_fetch_object` и `mysql_fetch_row` в `classes/world.class.php` и `classes/character.class.php`.
- Это ломает совместимость с PHP 8 (расширение `mysql` давно удалено).

### 3) Широкое подавление ошибок и небезопасная обработка input
- Повсеместное `@` (например `@$_POST`, `@mysqli_*`) усложняет диагностику и скрывает реальные проблемы.
- Action routing в `index.php` опирается на нестрогую обработку пользовательского ввода.

### 4) Нет зависимостей/CI-контроля
- Нет `composer.json`, нет базовых quality-gates (lint, static analysis, tests).

---

## Рекомендуемая стратегия миграции

### Быстрый фикс (с минимальным риском)
- Убрать остатки `mysql_*` и перевести их на существующий `MYSQL::g(...)->query()` + `mysqli_fetch_*`.
- Добавить временный shim/adapter для унификации выдачи строк (ассоц/объект), чтобы не ломать весь код сразу.
- Включить строгий режим ошибок БД хотя бы в dev (`mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)`).

### Правильное решение
- Ввести новый слой `Database` на PDO:
  - единый DSN для login/game серверов,
  - prepared statements,
  - транзакции,
  - typed fetch helpers (`fetchOne`, `fetchAll`, `fetchValue`).
- Постепенно переписать domain-классы (`account`, `world`, `character`, `core`) на PDO API.
- После стабилизации — обновить шаблонизатор до Smarty 4/5 или заменить на Twig (желательно вторым этапом).

---

## TODO roadmap (PR-by-PR)

## PR-1: PHP 8 Compatibility Baseline (без смены архитектуры)

**Цель:** проект запускается на PHP 8.2+, без fatal из-за `mysql_*`.

### Scope
- [ ] Заменить `mysql_real_escape_string` на текущий escape через connection (`MYSQL::g()->escape_string`).
- [ ] Заменить `mysql_fetch_object/mysql_fetch_row` на `mysqli_fetch_object/mysqli_fetch_row`.
- [ ] Убрать критичные `@` вокруг DB-вызовов в боевом контуре.
- [ ] Добавить smoke-check script (`php -l` по ключевым файлам).

### Пример патча (unified diff)
```diff
--- a/classes/world.class.php
+++ b/classes/world.class.php
@@ -23,7 +23,7 @@ class world {
-        $this->id = mysql_real_escape_string($id);
+        $this->id = MYSQL::g()->escape_string((string)$id);
@@ -71,7 +71,7 @@ class world {
-        while ($row = @mysql_fetch_object($rslt)) {
+        while ($row = mysqli_fetch_object($rslt)) {
@@ -109,7 +109,7 @@ class world {
-        while ($row = @mysql_fetch_object($rslt)) {
+        while ($row = mysqli_fetch_object($rslt)) {
 }
```

**Риски:** низкие, но возможны скрытые warning'и из-за удаления `@`.

---

## PR-2: Введение PDO-слоя (parallel run)

**Цель:** добавить PDO abstraction без мгновенного удаления старого `MYSQL` класса.

### Scope
- [ ] Добавить `classes/database.class.php` (PDO singleton/factory по server-id).
- [ ] Реализовать методы:
  - [ ] `query(string $sql, array $params = []): PDOStatement`
  - [ ] `fetchOne(string $sql, array $params = []): ?array`
  - [ ] `fetchValue(string $sql, array $params = [], int $column = 0): mixed`
  - [ ] `execute(string $sql, array $params = []): int`
- [ ] Конфигурировать:
  - [ ] `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`
  - [ ] `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`
  - [ ] `PDO::ATTR_EMULATE_PREPARES => false`

### Готовый код (скелет)
```php
<?php

declared(strict_types=1);

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

        $cfg = $serverId === null
            ? CONFIG::g()->login_server
            : CONFIG::g()->select_game_server($serverId);

        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $cfg['hostname'], $cfg['database']);

        $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        self::$pool[$key] = $pdo;
        return $pdo;
    }

    public static function fetchValue(string $sql, array $params = [], ?int $serverId = null, int $column = 0): mixed
    {
        $stmt = self::conn($serverId)->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn($column);
        return $value === false ? null : $value;
    }
}
```

**Риски:** средние (различия типов, charset, SQL-mode).

---

## PR-3: Миграция доменных классов на PDO

**Цель:** убрать зависимость от `classes/mysql.class.php` в business-контуре.

### Scope
- [ ] `classes/account.class.php` → prepared statements.
- [ ] `classes/world.class.php` → PDO fetch.
- [ ] `classes/character.class.php` → PDO fetch + параметризация всех ID/login полей.
- [ ] Удалить остатки строковой конкатенации SQL с пользовательским вводом.

### Что даст по перформансу
- Стабильное поведение prepared statements.
- Меньше лишних escape-аллокаций строк.
- Проще профилировать «дорогие» SQL, потому что исчезнут подавленные ошибки.

---

## PR-4: Обновление библиотек и bootstrap

**Цель:** перейти с vendored legacy libs на поддерживаемые пакеты.

### Scope
- [ ] Добавить `composer.json`.
- [ ] Обновить шаблонизатор:
  - [ ] вариант A (минимальный риск): Smarty 4 + адаптер совместимости;
  - [ ] вариант B (правильный путь): Twig + постепенная миграция шаблонов.
- [ ] Почтовый слой:
  - [ ] убрать legacy SMTP/mail-signature реализацию;
  - [ ] перейти на `symfony/mailer` или свежий `phpmailer/phpmailer`.

**Риски:** высокие для полного шаблонного апдейта; лучше катить поэтапно.

---

## PR-5: CI/качество/безопасность

**Цель:** зафиксировать регрессии до продакшена.

### Scope
- [ ] GitHub Actions: PHP 8.2 + 8.3 matrix.
- [ ] `php -l`, `phpcs` (или `php-cs-fixer`), `phpstan` (уровень постепенно поднимать).
- [ ] Базовые интеграционные smoke-тесты auth/registration/change-password.
- [ ] Security checks (composer audit, secret scan).

---

## Приоритеты внедрения

1. **Срочно:** PR-1 (иначе PHP8 runtime нестабилен/неработоспособен).
2. **Сразу после:** PR-2 + PR-3 (PDO + безопасность SQL).
3. **Планово:** PR-4 + PR-5 (libraries + CI hardening).

---

## Предлагаемые commit messages

- `chore(php8): remove legacy mysql_* calls and stabilize runtime compatibility`
- `feat(db): introduce PDO database abstraction for login and game servers`
- `refactor(domain): migrate account/world/character queries to prepared statements`
- `build(deps): add composer and upgrade template/mail libraries`
- `ci: add php8 matrix checks, lint, static analysis and smoke tests`
