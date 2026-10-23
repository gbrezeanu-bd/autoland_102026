<?php
// get config file;
include_once './includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/vendor/PHPMailer/src/SMTP.php';

// Set optimized page title (direct in file for easier maintenance)
$page_title = "Cerere Ofertă Personalizată | AutoLand România";

// Set SEO meta tags BEFORE header
require_once './includes/meta_tags.php';
setRequestOfferPageMeta();

// get header
include './themes/automotive/template_parts/header.php';

$footer_scripts .= '';

$footer_scripts .= "<script src='https://www.google.com/recaptcha/api.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/jquery.validate.min.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/additional-methods.min.js'></script>";
$footer_scripts .= "<script type='text/javascript'>" . file_get_contents("./includes/validate_cerere_oferta.js") . "</script>";


// send the funcking message now

// Emailul către firmă: câmpurile completate, grupate pe secțiuni, într-un tabel simplu (Arial), cu diacritice corecte (UTF-8)
$sectiuni = array(
    'Date personale' => array(
        't1'  => 'Nume și prenume',
        't3'  => 'Județul',
        't10' => 'În calitate de',
        't11' => 'Firma este plătitoare de TVA',
    ),
    'Mașina dorită' => array(
        't12' => 'Tip de vehicul',
        't13' => 'Marca',
        't14' => 'Modelul',
        't15' => 'Specificații dorite',
        't16' => 'Tipul caroseriei',
        't17' => 'An fabricație minim',
        't18' => 'Preț până la (EUR)',
        't19' => 'Combustibil',
        't20' => 'Capacitate cilindrică',
        't21' => 'Cutie de viteze',
        't23' => 'Rulaj maxim (km)',
        't26' => 'Alte dotări solicitate',
    ),
    'Buget și finanțare' => array(
        't27' => 'Doresc achiziția în',
        't28' => 'Prețul în țară',
    ),
    'Contact' => array(
        'tel'       => 'Telefon',
        'email'     => 'Email',
        'client_ip' => 'IP client',
    ),
);
$esc = function ($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); };
$message_body = '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.5; color: #222222;">'
    . '<h2 style="margin: 0 0 14px; font-size: 20px; color: #222222;">Solicitare de ofertă personalizată</h2>';
$message_text = "Solicitare de ofertă personalizată\n";
foreach ($sectiuni as $titlu => $campuri) {
    $randuri = '';
    $text = '';
    foreach ($campuri as $nume => $eticheta) {
        $valoare = isset($_POST[$nume]) && is_string($_POST[$nume]) ? trim($_POST[$nume]) : '';
        if ($valoare === '' || $valoare === 'selecteaza') {
            continue;
        }
        $randuri .= '<tr><td style="padding: 5px 18px 5px 0; color: #666666; vertical-align: top; white-space: nowrap;">' . $esc($eticheta) . '</td>'
            . '<td style="padding: 5px 0; font-weight: bold; vertical-align: top;">' . nl2br($esc($valoare)) . '</td></tr>';
        $text .= $eticheta . ': ' . $valoare . "\n";
    }
    if ($randuri === '') {
        continue;
    }
    $message_body .= '<h3 style="margin: 18px 0 6px; padding-bottom: 4px; border-bottom: 1px solid #dddddd; font-size: 16px; color: #2b7a96;">' . $esc($titlu) . '</h3>'
        . '<table cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">' . $randuri . '</table>';
    $message_text .= "\n" . $titlu . "\n" . $text;
}
$message_body .= '</div>';

$to = $email_address;
$nume_client = isset($_POST['t1']) && is_string($_POST['t1']) ? trim($_POST['t1']) : '';
$subject = "Solicitare ofertă de la " . $nume_client;
$mail = new PHPMailer(TRUE);
$error = FALSE;

try {
    // fără aceste două setări PHPMailer trimite în ISO-8859-1 și diacriticele apar stricate
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = $email_host;
    if (!empty($email_username) && !empty($email_password)) {
        $mail->SMTPAuth = TRUE;
        $mail->Username = $email_username;
        $mail->Password = $email_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
    }

    $mail->setFrom($email_address, $subject);
    $mail->addAddress($email_address, $subject);
    $mail->addReplyTo($email_address, $subject);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message_body;
    $mail->AltBody = $message_text;
    $mail->send();
    $error = FALSE;
} catch (Exception $e) {
    //echo $e->getTraceAsString();
    $error = TRUE;
}
?>


<section class="content">
    <div class="container">
        <div class="inner-page row">
            <!-- Sidebar START Here -->
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 padding-right-none sm-padding-left-none xs-padding-left-none padding-bottom-40 scroll_effect fadeInUp" data-wow-delay='.2s' style="z-index:100">
                <?php
                require_once './themes/automotive/template_parts/sidebar_left.php';
                ?>
            </div>
            <!-- Sidebar END Here -->


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp">

                <?php
                // get car listing (all or just a part)
                //require_once './themes/automotive/template_parts/inventory_listing2.php';
                ?>
                <div class="information_head margin-top-45">
                    <?php
                    if ($error == FALSE) {
                        ?>
                        <h3 class="margin-bottom-45">Solicitarea de oferta a fost trimisa</h3>
                        <p>
                            Mesajul de solicitare de oferta a fost trimis. Va multumim!
                        </p>
                        <?php
                    } else {
                        ?>
                        <h3 class="margin-bottom-45">Solicitarea de oferta nu a fost trimisa</h3>
                    <p>
                        Mesajul de solicitare de oferta nu a fost trimis, deoarece a aparut o eroare. Va rugam sa incercati din nou!
                    </p>
                        <?php
                    }
                    ?>

                    <p>
                        <?php //print $message_body  ?>
                    </p>
                </div>


            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
