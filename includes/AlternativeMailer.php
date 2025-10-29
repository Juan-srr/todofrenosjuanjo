<?php
// Mailer alternativo que usa solo funciones nativas de PHP
class AlternativeMailer {
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Detectar si estamos en Hostinger
        $is_hostinger = (strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false || 
                        strpos($_SERVER['HTTP_HOST'], '.com') !== false ||
                        strpos($_SERVER['HTTP_HOST'], '.net') !== false);
        
        if ($is_hostinger) {
            $this->from_email = 'jhontdj@todofrenosjuanjo.shop';
            $this->from_name = 'TodoFrenos - Sistema de Inventario';
        } else {
            $this->from_email = 'jhonjfrenosj@gmail.com';
            $this->from_name = 'TodoFrenos - Sistema de Verificación';
        }
    }
    
    public function enviarCodigoVerificacion($correo, $codigo, $tipo = 'registro') {
        try {
            $asunto = ($tipo === 'registro') ? 
                'Verificación de tu cuenta - TodoFrenos' : 
                'Código de verificación - TodoFrenos';
            
            $mensaje = $this->getTemplate($codigo, $tipo);
            $headers = $this->getHeaders();
            
            // Usar función mail() nativa de PHP
            $resultado = mail($correo, $asunto, $mensaje, $headers);
            
            if ($resultado) {
                error_log("Correo enviado exitosamente a: $correo");
                return true;
            } else {
                error_log("Error enviando correo a: $correo");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Excepción enviando correo: " . $e->getMessage());
            return false;
        }
    }
    
    private function getHeaders() {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "X-Priority: 3\r\n";
        
        return $headers;
    }
    
    private function getTemplate($codigo, $tipo) {
        $titulo = ($tipo === 'registro') ? '¡Bienvenido a TodoFrenos!' : 'Verificación de inicio de sesión';
        $mensaje = ($tipo === 'registro') ? 
            'Gracias por registrarte en nuestro sistema. Para completar tu registro, necesitamos verificar tu correo electrónico.' :
            'Se ha detectado un nuevo inicio de sesión en tu cuenta. Para continuar, ingresa el siguiente código:';
        $tiempo = ($tipo === 'registro') ? '10 minutos' : '5 minutos';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$titulo}</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                
                <!-- Header -->
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #333; margin: 0; font-size: 28px;'>{$titulo}</h1>
                </div>
                
                <!-- Mensaje principal -->
                <div style='margin-bottom: 30px;'>
                    <p style='color: #666; font-size: 16px; line-height: 1.5; margin: 0;'>{$mensaje}</p>
                </div>
                
                <!-- Código de verificación -->
                <div style='background: linear-gradient(135deg, #ffe600, #ffd700); padding: 25px; text-align: center; margin: 25px 0; border-radius: 10px; border: 2px solid #ffd700;'>
                    <h3 style='color: #333; margin: 0 0 15px 0; font-size: 18px;'>Tu código de verificación es:</h3>
                    <div style='background-color: #ffffff; padding: 15px; border-radius: 8px; display: inline-block; border: 2px solid #333;'>
                        <h1 style='color: #333; font-size: 36px; letter-spacing: 8px; margin: 0; font-family: monospace; font-weight: bold;'>{$codigo}</h1>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div style='margin-bottom: 30px;'>
                    <p style='color: #666; font-size: 14px; line-height: 1.5; margin: 0;'>
                        Este código expirará en <strong>{$tiempo}</strong>. 
                        " . ($tipo === 'registro' ? 'Si no solicitaste este registro, puedes ignorar este correo.' : 'Si no fuiste tú quien inició sesión, cambia tu contraseña inmediatamente.') . "
                    </p>
                </div>
                
                <!-- Footer -->
                <div style='border-top: 2px solid #f0f0f0; padding-top: 20px; text-align: center;'>
                    <p style='color: #333; font-weight: bold; margin: 0; font-size: 16px;'>Saludos,<br>Equipo TodoFrenos</p>
                    <p style='color: #999; font-size: 12px; margin: 10px 0 0 0;'>
                        Este es un correo automático, por favor no respondas a este mensaje.
                    </p>
                </div>
                
            </div>
        </body>
        </html>";
    }
}
?>
