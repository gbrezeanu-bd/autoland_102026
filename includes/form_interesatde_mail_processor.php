<?php

header('Content-type: application/json; charset=UTF-8'); // set content type with UTF-8 encoding
// this code is ready for SMTP authentication, and is using it if email password is provided
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
//
//require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/Exception.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/PHPMailer.php';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/SMTP.php';


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
$to_dest=$data['vizEmail'];
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
    $temp_test = '';
    $key2= $key;
    if ($pos !== false) {
        $key2=after('viz',$key);
    }
    if (strcmp($key2, "Url") === 0) {
        $value = '<a href="'.$value.'">'.$value."</a>";
    }
    $body .= ucfirst($key2) . ": " . $value. "<br />";
}

/*
foreach ($data as $key => $value) {
    $body .= ucfirst($key) . ": " . $value . "<br />";
}
*/

$body .= 'IP: ' . getClientIP();


// send mail to admin
$mail_response= sendSMTPEmail($to, $subject, $body);

if($mail_response['status']!=='success'){
    print json_encode($mail_response);
    exit;
}

// send mail to the person interested in
$to=$_POST['vizEmail'];
$subject="Va multumim pentru interesul acordat Autoland";
$body='';
$body.='Stimate Domnule / Doamna '. $_POST['vizNume']. "<br />";
$body.="Va multumim pentru interesul acordat Autoland Romania si va felicitam pentru ca ati reusit sa ne gasiti! <br /> Tot ce va mai trebuie acum pentru a va afla cat mai curand la volanul masinii de care aveti nevoie este putina incredere, cu atat mai mult daca acum sunteti abia la prima colaborare cu noi! <br /> Va informam inca de la inceput ca pentru a putea cumpara o masina de ocazie prin oferta noastra de finantare in rate/leasing pentru o perioada intre 1 si 5 ani este necesar un avans minim de 10...15% si trebuie intocmit un dosar de finantare. <br /> Se pot finanta si persoanele fizice care muncesc in afara (ITALIA, SPANIA, GERMANIA, etc) cu venituri din strainatate asadar, pentru avans incepand cu 25-30%. <br /> Este obligatoriu insa pentru orice masina achizitionata in rate/leasing sa fie efectuata intai o analiza financiara in vederea stabilirii conditiilor de finantare, etapa pentru care va fi necesar sa ne trimiteti (de preferat prin email) cateva documente si formulare specifice. <br /> Ulterior veti primi de la noi un raspuns oficial intr-un termen mediu de 2 zile lucratoare, moment in care se stabileste practic structura de finantare finala si se rezerva masina. <br /> Documentele necesare pentru fiecare caz in parte se regasesc pe pagina Rate/Leasing de pe site, fiind necesare impreuna cu formularele aferente fiecarui dosar in parte, completate in mod corespunzator. <br /> Masina dumneavoastra va fi ridicata de la noi in cel mai scurt timp posibil, avand intre timp atat actele auto la zi cat si starea tehnica verificata temeinic (mecanic, electric si electronic-diagnoza), din locatia IASI-ROMANIA. <br /> La livrare masina dumneavoastra se va afla de fiecare data intr-o stare tehnica foarte buna, curata atat la exterior dar mai ales la interior, cu tapiteria complet igienizata de fiecare data (cu aburi), cu toate actele pregatite conform legislatiei actuale in vederea livrarii dar mai ales verificata tehnic si pregatita de livrare cu o perioada de garantie la alegere intre 6 luni si 2 ani. <br /> Pentru orice detaliu suplimentar va rugam sa nu ezitati sa ne contactati pe email sau telefonic intre orele 9-18 de Luni pana Vineri la unul din numerele de la pagina CONTACT! <br /> Vom incerca sa va raspundem in cel mai scurt timp posibil, deviza noastra fiind aceea de a oferi Solutii, nu Scuze! <br /><br />Va multumim anticipat pentru timpul acordat si speram intr-o buna colaborare cu dumneavoastra, <br />Cu respect, Echipa Autoland Romania.";


// send confimation mail to the person
$mail_response= sendSMTPEmail($to_dest, $subject, $body);

if($mail_response['status']!=='success'){
    print json_encode($mail_response);
    exit;
}

print json_encode(['status' => 'success']);
exit;



//function sendEmail($to, $subject, $body) {
//    global $email_username;
//    global $email_host;
//    global $email_password;
//
//    $mail = new PHPMailer(TRUE); // enable exception
//
//    try {
//        $mail->SMTPDebug = 0;
//        $mail->isSMTP();
//        $mail->Host = $email_host;
//        if (!empty($email_username) && !empty($email_password)) {
//            $mail->SMTPAuth = TRUE;
//            $mail->Username = $email_username;
//            $mail->Password = $email_password;
//            $mail->SMTPSecure = 'tls';
//            $mail->Port = 587;
//            $body .= "Acest mesaj a folosit autentificare SMTP";
//        }
//
//        $mail->setFrom($to, $subject);
//        $mail->addAddress($to, $subject);
//        $mail->addReplyTo($to, $subject);
//
//        $mail->isHTML(true);
//        $mail->Subject = $subject;
//        $mail->Body = $body;
//
//        $mail->send();
//    
//        return ['status' => 'success'];
//        
//        } catch (Exception $exc) {
//        $err= str_replace( ":", " ", $mail->ErrorInfo);
//        return ['status' => 'error', 'error' => $err];
//    }
//}

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
