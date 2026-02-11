# PR-4: PHPMailer без Composer (временный режим)

Текущая реализация использует адаптер `classes/phpmailer.class.php` и автоматически включает PHPMailer,
если в проекте присутствуют файлы:

- `libs/phpmailer/src/Exception.php`
- `libs/phpmailer/src/PHPMailer.php`
- `libs/phpmailer/src/SMTP.php`

Если этих файлов нет, система **автоматически откатывается** на legacy-отправку (`SMTP`/`mail()`) без падения runtime.

## Как подключить PHPMailer вручную

1. Скачайте релиз PHPMailer `6.x`.
2. Скопируйте 3 файла из `src/` в `libs/phpmailer/src/`.
3. Ничего в `config.php` менять не нужно — используется текущая SMTP-конфигурация.

Это переходный шаг до полной миграции на Composer.
