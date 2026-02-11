<?php

defined( '_ACM_VALID' ) or die( 'Direct Access to this location is not allowed.' );

class Mailer
{
    private $mailer;
    private $lastError = '';

    public static function isAvailable()
    {
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            return true;
        }

        $base = __DIR__ . '/../libs/phpmailer/src/';
        $required = ['Exception.php', 'PHPMailer.php', 'SMTP.php'];

        foreach ($required as $file) {
            if (!is_file($base . $file)) {
                return false;
            }
        }

        require_once $base . 'Exception.php';
        require_once $base . 'PHPMailer.php';
        require_once $base . 'SMTP.php';

        return class_exists('PHPMailer\\PHPMailer\\PHPMailer');
    }

    public static function create(array $config)
    {
        $instance = new self();
        $instance->bootstrap($config);
        return $instance;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function send($to, $fromName, $fromEmail, $subject, $htmlBody)
    {
        if (!$this->mailer) {
            $this->lastError = 'PHPMailer не инициализирован.';
            return false;
        }

        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->setFrom($fromEmail, $fromName);
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = strip_tags(str_replace('<br />', "\n", $htmlBody));

            if (!$this->mailer->send()) {
                $this->lastError = $this->mailer->ErrorInfo;
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    private function bootstrap(array $config)
    {
        if (!self::isAvailable()) {
            $this->mailer = null;
            return;
        }

        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);

        $mailer->CharSet = !empty($config['charset']) ? $config['charset'] : 'UTF-8';
        $mailer->isHTML(true);

        if (!empty($config['use_smtp'])) {
            $mailer->isSMTP();
            $mailer->Host = (string)$config['host'];
            $mailer->Port = (int)$config['port'];
            $mailer->SMTPAuth = true;
            $mailer->Username = (string)$config['username'];
            $mailer->Password = (string)$config['password'];

            if (!empty($config['helo'])) {
                $mailer->Helo = (string)$config['helo'];
            }
        } else {
            $mailer->isMail();
        }

        $this->mailer = $mailer;
    }
}
