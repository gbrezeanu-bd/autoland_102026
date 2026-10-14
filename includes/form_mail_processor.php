<?php

header('Content-type: application/json; charset=UTF-8'); // set content type with UTF-8 encoding
// this code is ready for SMTP authentication, and is using it if email password is provided
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/SMTP.php';


// never trust the user...
$data = $_POST;

// cleanup unrequired $_POST
$data = cleanupPost($data);

// Validate data - Use htmlspecialchars instead of htmlentities to preserve UTF-8 diacritics
foreach ($data as $key => $post_data) {
    // Trim whitespace
    $data[$key] = trim($post_data);
    // Use htmlspecialchars to protect against XSS while preserving diacritics
    $data[$key] = htmlspecialchars($data[$key], ENT_QUOTES, 'UTF-8');
}

// *****************************************************************************
// create mail data
$to = $email_address;
$subject = $data['form_title'];
array_shift($data);

$body = '';

// functia after afiseaza tot ce este intr-un string dupa start point 
// Ex: after ('@','alex@mestermic.ro') - afiseaza mestermic.ro

function after ($start, $stop)
{
    if (!is_bool(strpos($stop, $start)))
        return substr($stop, strpos($stop,$start)+strlen($start));
};

foreach ($data as $key => $value) {
    $pos = strpos($key, 'viz');
    $key2= $key;
    if ($pos !== false) {
        $key2=after('viz',$key);
    }
    $body .= ucfirst($key2) . ": " . $value. "<br />";
}

$body .= 'IP: ' . getClientIP();

//$body = wordwrap($body, 65);

// *****************************************************************************

$mail = new PHPMailer(TRUE); // enable exception

try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = $email_host;
    if (!empty($email_username) && !empty($email_password)) {
        $mail->SMTPAuth = TRUE;
        $mail->Username = $email_username;
        $mail->Password = $email_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $body .= "<br \>Acest mesaj a folosit autentificare SMTP";
    }

    $mail->addReplyTo('office@autolandromania.ro', 'Office Autoland');
    $mail->setFrom($email_address, 'Office Autoland');
    $mail->addAddress('office@autolandromania.ro', 'Office Autoland');
//    $mail->addAddress($email_address, $subject);
//    $mail->addReplyTo($email_address, $subject);

    // Set UTF-8 encoding to properly handle Romanian diacritics (ă, â, î, ș, ț)
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
    print json_encode(['status' => 'success']);
    exit(0);
} catch (Exception $e) {
    //echo $e->getTraceAsString();
    $err= str_replace( ":", " ", $mail->ErrorInfo);
    print json_encode(['status' => 'error', 'error' => $err]);
}
exit;


//print json_encode(['status' => 'error', 'data' => $body]);
//print json_encode(['status' => 'error', 'data' => $data]);
//exit();
//return json_encode(['status' => 'OK']);
//return "Foobar";
//return $_POST;

/**
 * Cleanup unnecessary POST values
 * 
 * @param type $data
 * @return type
 */
function cleanupPost(&$data) {
    // cleanup undesired values
    if (array_key_exists('g-recaptcha-response', $data)) {
        unset($data['g-recaptcha-response']);
    }
    if (array_key_exists('hiddenRecaptcha', $data)) {
        unset($data['hiddenRecaptcha']);
    }
    if (array_key_exists('agree', $data)) {
        unset($data['agree']);
    }

    // remove submit button
    if (array_key_exists('submit_btn', $data)) {
        unset($data['submit_btn']);
    }

    return $data;
}
