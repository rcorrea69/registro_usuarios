<?php 

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
//Create an instance; passing `true` enables exceptions
function enviarmail($email,$apellido,$asunto,$cuerpo){
$mail = new PHPMailer(true);


try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->SMTPDebug = 0;                        //Habilitar la salida de depuración detallada 
    $mail->isSMTP();                                            //Enviar usando SMTP
    $mail->Host       = 'smtp.hostinger.com';                     //Configure el servidor SMTP para enviar a través 
    $mail->SMTPAuth   = true;                                   //Habilitar autenticación SMTP
    $mail->Username   = 'rcorrea@mpcargentina.net';                     //SMTP username
    $mail->Password   = '@Rc0rr34';  
    //$mail->Password   = '$MPCargentina2022';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Habilitar el cifrado TLS implícito 
    $mail->Port       = 465;                                    //Puerto TCP al que conectarse; use 587 si configuró `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('rcorrea@mpcargentina.net', 'tucu prode');
    $mail->addAddress( $email, $apellido);     //Agregue un destinatario 
    $mail->addAddress( 'rcorrea@mpcargentina.net', $apellido);


    
    //Content
    $mail->isHTML(true); //Set email format to HTML

    $mail->Subject = $asunto;
    $mail->Body    = $cuerpo;
    // $mail->Subject = utf8_decode('Solicitud de Afiliación a AGAE');
    // $mail->Body    = $mensaje;
    $mail->AltBody = 'Este es el cuerpo en texto sin formato para clientes de correo que no son HTML';


    $mail->send();
    //echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
};
?>
