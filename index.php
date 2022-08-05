<?php
require_once 'app/config.php';
//insertar un concepto directamente al array 
$_SESSION['new_quote']['items'] =
    [
        [
            'id' => 1234,
            'concept' => 'Concepto 1',
            'type' => 'producto',
            'quantity' => 1,
            'price' => 100,
            'taxes' => (TAXES_RATE / 100) * (100 * 2),
            'total' => (100 * 2) + ((TAXES_RATE / 100) * (100 * 2)),
        ]
    ];

// prueba phpmailer 
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
// use PHPMailer\PHPMailer\Exception;

// try {
//     //data
//     $data =[
//         'name'=> 'Walter Hernan',
//         'email'=> 'tony@localhost.com',
//         'subject' => 'Prueba de correo',
//         'body' => '<h1>Hola mundo</h1>',
//         'alt_text' => 'Este es un correo de prueba',
//     ];
//     send_mail($data);
//     echo 'Message has been sent';
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }

//renderizando las vistas
get_view('index');
