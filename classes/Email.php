<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'e2d131f3244426';
        $mail->Password = 'a886d08799162a';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre. "</strong> Has creado tu cuenta en UpTask, solo debes confirmarla en este enlace.</p>";
        $contenido .= "<p>Presione aqui: <a href='http://localhost:3000/confirmar?token=" .$this->token . "'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, ignora este mensaje.</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'e2d131f3244426';
        $mail->Password = 'a886d08799162a';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');
        $mail->Subject = 'Reestablece tu password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre. "</strong> Parece que has olvidado tu password, sigue este enlace para recuperarlo.</p>";
        $contenido .= "<p>Presione aqui: <a href='http://localhost:3000/reestablecer?token=" .$this->token . "'>Reestablecer password</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, ignora este mensaje.</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }

}


?>