<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function get_view($view_name)
{
    $view_name = $view_name . '.php';
    $view_path = VIEWS . $view_name;
    if (!file_exists($view_path)) {
        die('View not found: ' . $view_path);
    } else {
        require_once $view_path;
    }
}

// cotizacion 
function get_quote()
{
    if (!isset($_SESSION['new_quote'])) {
        return $_SESSION['new_quote'] = array(
            'number' => rand(1111111, 999999),
            'name' => '',
            'company' => '',
            'email' => '',
            'items' => [],
            'subtotal' => 0,
            'taxes' => 0,
            'shipping' => 0,
            'tota' => 0,
        );
    }
    // recalcular todos los quotes
    recalculate_quote();
    return $_SESSION['new_quote'];
}

function set_client($client)
{
    $_SESSION['new_quote']['name'] = trim($client['nombre']);
    $_SESSION['new_quote']['company'] = trim($client['empresa']);
    $_SESSION['new_quote']['email'] = trim($client['email']);
    return true;
}

function recalculate_quote()
{
    $items = [];
    $subtotal = 0;
    $taxes = 0;
    $shipping = SHIPPING;
    $total = 0;
    if (!isset($_SESSION['new_quote'])) {
        return false;
    }
    // validar item
    $items = $_SESSION['new_quote']['items'];

    if (!empty($items)) {
        foreach ($items as $item) {
            $subtotal += $item['total'];
            $taxes += $item['taxes'];
        }
    }
    // $shipping = $_SESSION['new_quote']['shipping'];
    $total = $subtotal + $taxes + $shipping;
    $_SESSION['new_quote']['subtotal'] = $subtotal;
    $_SESSION['new_quote']['taxes'] = $taxes;
    $_SESSION['new_quote']['shipping'] = $shipping;
    $_SESSION['new_quote']['total'] = $total;
    return true;
}
function restart_quote()
{
    $_SESSION['new_quote'] = array(
        'number' => rand(1111111, 999999),
        'name' => '',
        'company' => '',
        'email' => '',
        'items' => [],
        'subtotal' => 0,
        'taxes' => 0,
        'shipping' => 0,
        'tota' => 0,
    );
    return true;
}

function get_items()
{
    $items = [];
    if (!isset($_SESSION['new_quote']['items'])) {
        return $items;
    }
    $items = $_SESSION['new_quote']['items'];
    return $items;
}
function get_item($id)
{
    $items = get_items();
    if (!empty($items)) {
        foreach ($items as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
    }
    return false;
}
function delete_items()
{
    $_SESSION['new_quote']['items'] = [];
    recalculate_quote();
    return true;
}
function delete_item($id)
{
    $items = get_items();
    if (empty($items)) {
        return false;
    }
    foreach ($items as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['new_quote']['items'][$key]);
            return true;
        }
    }
    return false;
}
function add_item($item)
{
    $items = get_items();

    if (get_item($item['id']) !== false) {
        foreach ($items as $key => $e_item) {
            if ($e_item['id'] == $item['id']) {
                $_SESSION['new_quote']['items'][$key] = $item;
                return true;
            }
        }
    }
    $_SESSION['new_quote']['items'][] = $item;
    return true;
}
function json_build($status = 200, $data = null, $msg = '')
{
    if (empty($msg) || $msg == '') {
        switch ($status) {
            case 200:
                $msg = 'OK';
                break;
            case 201:
                $msg = 'Created';
                break;
            case 204:
                $msg = 'No Content';
                break;
            case 400:
                $msg = 'Bad Request';
                break;
            case 401:
                $msg = 'Unauthorized';
                break;
            case 403:
                $msg = 'Forbidden';
                break;
            case 404:
                $msg = 'Not Found';
                break;
            case 500:
                $msg = 'Internal Server Error';
                break;
            case 550:
                $msg = 'Permission Denied';
                break;
            default:
                $msg = 'Unknown Error';
                break;
        }
    }

    $json = [
        'status' => $status,
        'msg' => $msg,
        'data' => $data,
    ];

    return json_encode($json);
}
function json_output($json)
{
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=UTF-8');
    if (is_array($json)) {
        $json = json_encode($json);
    }
    echo $json;
    exit();
}

function get_module($view, $data = [])
{
    $view = $view . '.php';
    if (!is_file($view)) {
        return false;
    }
    $d = $data = json_decode(json_encode($data)); //convertir a objeto
    ob_start();
    require_once $view;
    $output = ob_get_clean();
    return $output;
}
function hook_get_quote_res()
{
    $quote = get_quote();
    $html = get_module(MODULES . 'quote_table', $quote);
    return json_output(json_build(200, ['quote' => $quote, 'html' => $html]));
}

// agregar conceptos
function hook_add_to_quote()
{
    // validar
    if (!isset($_POST['concepto'], $_POST['tipo'], $_POST['precio_unitario'], $_POST['cantidad'])) {
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }
    $concepto = trim($_POST['concepto']);
    $type = trim($_POST['tipo']);
    $price = (float) str_replace([',', '$'], '', $_POST['precio_unitario']);
    $quantity = (int) trim($_POST['cantidad']);
    $subtotal = (float) $price * $quantity;
    $taxes = (float) $subtotal * (TAXES_RATE / 100);

    $item = [
        'id' => uniqid(),
        'concept' => $concepto,
        'type' => $type,
        'quantity' => $quantity,
        'price' => $price,
        'taxes' => $taxes,
        'total' => $subtotal
    ];

    if (!add_item($item)) {
        json_output(json_build(400, null, 'Error al agregar concepto.'));
    }
    json_output(json_build(201, get_item($item['id']), 'Concepto agregado exitosamente.'));
}

// reiniciar cotizacion
function hook_restart_quote()
{
    $items = get_items();
    if (empty($items)) {
        json_output(json_build(400, null, 'No es necesario reiniciar cotizacion ya que no hay conceptos en esta.'));
    }
    if (!restart_quote()) {
        json_output(json_build(400, null, 'Error al reiniciar cotizacion.'));
    }
    json_output(json_build(200, null, 'Cotizacion reiniciada exitosamente.'));
}

// eliminar concepto
function hook_delete_concept()
{
    if (!isset($_POST['id'])) {
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }
    $id = $_POST['id'];
    if (!delete_item($id)) {
        json_output(json_build(400, null, 'Error al eliminar concepto.'));
    }
    json_output(json_build(200, get_quote(), 'Concepto eliminado exitosamente.'));
}

// cargar concepto para editar
function hook_edit_concept()
{
    if (!isset($_POST['id'])) {
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }
    $id = $_POST['id'];
    $item = get_item($id);
    if (empty($item)) {
        json_output(json_build(400, null, 'Error al cargar concepto.'));
    }
    json_output(json_build(200, $item, 'Concepto cargado exitosamente.'));
}

// guardar los cambios de un concepto
function hook_save_concept()
{
    if (!isset($_POST['id_concepto'], $_POST['concepto'], $_POST['tipo'], $_POST['precio_unitario'], $_POST['cantidad'])) {
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }
    $id = $_POST['id_concepto'];
    $concept = trim($_POST['concepto']);
    $type = trim($_POST['tipo']);
    $price = (float) str_replace([',', '$'], '', $_POST['precio_unitario']);
    $quantity = (int) trim($_POST['cantidad']);
    $subtotal = (float) $price * $quantity;
    $taxes = (float) $subtotal * (TAXES_RATE / 100);

    $item = [
        'id' => $id,
        'concept' => $concept,
        'type' => $type,
        'quantity' => $quantity,
        'price' => $price,
        'taxes' => $taxes,
        'total' => $subtotal
    ];

    if (!add_item($item)) {
        json_output(json_build(400, null, 'Error al editar concepto.'));
    }
    json_output(json_build(201, get_item($id), 'Concepto editado exitosamente.'));
}



// generar un pdf
function generate_pdf($filename = null, $html, $save_to_file = true)
{
    $filename = $filename === null ? 'cotizacion_' . date('YmdHis') : $filename . '.pdf';
    $pdf = new Dompdf();
    $pdf->setPaper('A4');
    $pdf->loadHtml($html);
    $pdf->render();

    if ($save_to_file) {
        $output = $pdf->output();
        file_put_contents($filename, $output);
        return true;
    }


    $pdf->stream($filename, array('Attachment' => 0));
    return true;
}

// generar un pdf
function hook_generate_quote()
{
    if (!isset($_POST['nombre'], $_POST['empresa'], $_POST['email'])) {
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        json_output(json_build(400, null, 'Email invalido.'));
    }

    // GUARDAR INFORMACION DEL CLIENTE
    $client = [
        'nombre' => trim($_POST['nombre']),
        'empresa' => trim($_POST['empresa']),
        'email' => trim($_POST['email'])
    ];

    set_client($client);
    $quote = get_quote();

    if (empty($quote['items'])) {
        json_output(json_build(400, null, 'No hay conceptos en esta cotizacion.'));
    }

    $module = MODULES . 'pdf_template';
    $html = get_module($module, $quote);
    $filename = 'cotizacion_' . $quote['number'];
    $download = sprintf(URL . 'pdf.php?number=%s', $quote['number']);
    $quote['url'] = $download;

    // generar pdf y guardar en servidor

    if (!generate_pdf(UPLOADS . $filename, $html)) {
        json_output(json_build(400, null, 'Error al generar pdf.'));
    }
    json_output(json_build(200, $quote, 'Pdf generado exitosamente.'));
}

// cargar todas las cotizaciones
function get_all_quotes()
{
    return $quotes = glob(UPLOADS . 'cotizacion_*.pdf');
}


// redireccion 
function redirect($route)
{
    header(sprintf('Location: %s', $route));
    exit;
}

// evniar correo electrónico
function send_mail($data)
{
    $mail = new PHPMailer(true);
    try {
        //Recipients
        $mail->setFrom('tony@localhost.com', 'Tony Local'); //remitente
        $mail->addAddress($data['email'], empty($data['name']) ? null : $data['name']);     //destinatario
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $data['subject'];
        $mail->Body    = get_module(MODULES . 'email_template', $data); //plantilla
        $mail->AltBody = $data['alt_text'];
        $mail->CharSet = 'UTF-8';
        // adjuntos
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        if(!$mail->send()) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}


// enviar por email
function hook_send_quote(){
    if(!isset($_POST['number'])){
        json_output(json_build(403, null, 'Parametros incompletos.'));
    }

    // validar correo
    $number = $_POST['number'];
    $quote = get_quote();
    if(!filter_var($quote['email'],FILTER_VALIDATE_EMAIL)){
        json_output(json_build(400, null, 'Email invalido.'));
    }
    // VALIDAR SI EXISTE COTIZACION
    $file = sprintf(UPLOADS . 'cotizacion_%s.pdf', $number);
    if(!is_file($file)){
        json_output(json_build(400, null, 'No existe cotizacion.'));
    }
    // ENVIAR CORREO guardar informacion del cliente

    $body = '<h1>Nueva Cotizacion</h1> <br><p>Hola, <b>%s<b>,has recibido una nueva cotizacion con folio <b>%s</b> por parte de <b>%s</b>, se encuentra adjunta a este correo.</p>';
    $body = sprintf($body, $quote['name'], $number, APP_NAME);
    $email_data= [
        'subject' => sprintf('Nueva Cotizacion numero %s', $number),
        'alt_text' => sprintf('Nueva Cotizacion de %s', APP_NAME),
        'body' => $body,
        'name' => $quote['name'],
        'email' => $quote['email'],
        'attachments' => [$file]
    ];

    // generar pdf y guardar en el servidor
    if(!send_mail($email_data)){
        json_output(json_build(400, null, 'Error al enviar correo.'));
    }
    json_output(json_build(200, $quote, 'Correo enviado exitosamente.'));
    
}