<?php
// Detectar si estamos en Hostinger o entorno local
$is_hostinger = (strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false || 
                 strpos($_SERVER['HTTP_HOST'], '.com') !== false ||
                 strpos($_SERVER['HTTP_HOST'], '.net') !== false);

// Cargar configuración según el entorno
if ($is_hostinger) {
    require_once 'config/mail_hostinger.php';
} else {
    require_once 'config/mail.php';
}

require_once 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configurar();
    }
    
    private function configurar() {
        try {
            
            $this->mailer->isSMTP();
            $this->mailer->Host = MAIL_HOST;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = MAIL_USERNAME;
            $this->mailer->Password = MAIL_PASSWORD;
            
            
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS
            $this->mailer->Port = MAIL_PORT;
            $this->mailer->CharSet = 'UTF-8';
            
            
            $this->mailer->setFrom(MAIL_USERNAME, MAIL_FROM_NAME);
            
        } catch (Exception $e) {
            error_log("Error configurando PHPMailer: " . $e->getMessage());
        }
    }
    
    public function enviarCodigoVerificacion($correo, $codigo, $tipo = 'registro') {
        try {
            $this->mailer->addAddress($correo);
            
            if ($tipo === 'registro') {
                $this->mailer->Subject = 'Verificación de tu cuenta - TodoFrenos';
                $this->mailer->Body = $this->getTemplateRegistro($codigo);
            } else {
                $this->mailer->Subject = 'Código de verificación - TodoFrenos';
                $this->mailer->Body = $this->getTemplateLogin($codigo);
            }
            
            $this->mailer->isHTML(true);
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando correo: " . $e->getMessage());
            return false;
        }
    }
    
    private function getTemplateRegistro($codigo) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>¡Bienvenido a TodoFrenos!</h2>
            <p>Gracias por registrarte en nuestro sistema. Para completar tu registro, necesitamos verificar tu correo electrónico.</p>
            <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0;'>
                <h3 style='color: #007bff; margin: 0;'>Tu código de verificación es:</h3>
                <h1 style='color: #333; font-size: 32px; letter-spacing: 5px; margin: 10px 0;'>{$codigo}</h1>
            </div>
            <p>Este código expirará en 10 minutos. Si no solicitaste este registro, puedes ignorar este correo.</p>
            <p>Saludos,<br>Equipo TodoFrenos</p>
        </div>";
    }
    
    private function getTemplateLogin($codigo) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Verificación de inicio de sesión</h2>
            <p>Se ha detectado un nuevo inicio de sesión en tu cuenta. Para continuar, ingresa el siguiente código:</p>
            <div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0;'>
                <h3 style='color: #007bff; margin: 0;'>Tu código de verificación es:</h3>
                <h1 style='color: #333; font-size: 32px; letter-spacing: 5px; margin: 10px 0;'>{$codigo}</h1>
            </div>
            <p>Este código expirará en 5 minutos. Si no fuiste tú quien inició sesión, cambia tu contraseña inmediatamente.</p>
            <p>Saludos,<br>Equipo TodoFrenos</p>
        </div>";
    }
}
?>
