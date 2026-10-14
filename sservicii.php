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

$message_body = "";
$message_body .= "Subsemnatul " . $_POST['t1'] . "<br />";
$message_body .= "din judetul " . $_POST['t3'] . "<br />";
$message_body .= "In calitate de " . $_POST['t10'] . "<br />";
$message_body .= "din judetul " . $_POST['t11'] . "<br />";
$message_body .= "solicit ofertarea in vederea unei posibile achizitii ulterioare prin intermediul S.C. Autoland S.R.L. Iasi, a unui " . $_POST['t12'] . "<br />";
$message_body .= "marca " . $_POST['t13'] . "<br />";
$message_body .= "model " . $_POST['t14'] . "<br />";
$message_body .= "specificatii tehnice: " . $_POST['t15'] . "<br />";
$message_body .= "Tipul caroseriei: " . $_POST['t16'] . "<br />";
$message_body .= "an fabricatie minim: " . $_POST['t17'] . "<br />";
$message_body .= "an fabricatie maxim: " . $_POST['t18'] . "<br />";
$message_body .= "Motor: " . $_POST['t19'] . "<br />";
$message_body .= "capacitate (aproximativ): " . $_POST['t20'] . "<br />";
$message_body .= "Cutie de viteze: " . $_POST['t21'] . "<br />";
$message_body .= "Tractiune: " . $_POST['t22'] . "<br />";
$message_body .= "Km parcursi - maxim: " . $_POST['t23'] . "<br />";
$message_body .= "culoare: " . $_POST['t24'] . "<br />";
$message_body .= "numar usi: " . $_POST['t25'] . "<br />";
$message_body .= "alte dotari solicitate: " . $_POST['t26'] . "<br />";
$message_body .= "Doresc achizitia in: " . $_POST['t27'] . "<br />";
$message_body .= "iar pretul in tara: " . $_POST['t28'] . "<br />";
$message_body .= "sa fie intre (euro): " . $_POST['t29'] . "<br />";
$message_body .= "si (euro): " . $_POST['t30'] . "<br />";
$message_body .= "Telefon: " . $_POST['tel'] . "<br />";
$message_body .= "Email: " . $_POST['email'] . "<br />";
$message_body .= "Mobil: " . $_POST['mobil'] . "<br />";
$message_body .= "ClientIP: " . $_POST['client_ip'] . "<br />";

$to = $email_address;
$subject = "Solicitare oferta de la " . $_POST['t1'];

$mail = new PHPMailer(TRUE);
$error = FALSE;

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
        $body .= "Acest mesaj a folosit autentificare SMTP";
    }

    $mail->setFrom($email_address, $subject);
    $mail->addAddress($email_address, $subject);
    $mail->addReplyTo($email_address, $subject);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message_body . PHP_EOL . $body;

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
