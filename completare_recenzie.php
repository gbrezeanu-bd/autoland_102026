<?php
if (!array_key_exists('hash', $_REQUEST)) {
    http_response_code(404);
    exit;
}
if (empty($_REQUEST['hash']) || ($_REQUEST['hash'] == '')) {
    http_response_code(404);
    exit;
}

// get config file;
include_once './includes/config.php';

// verify hash
$hash = filter_var($_REQUEST['hash'], FILTER_SANITIZE_STRING);
$hashSQL = sprintf("SELECT * FROM recenzii2 WHERE hash='%s'", $hash);

$hashQuery = $connection->query($hashSQL) or die($connection->error);
$hashResult = mysqli_fetch_assoc($hashQuery);
//$hashCount= mysqli_num_rows($hashResult);

$_OK=FALSE;

if (is_null($hashResult)) {
    http_response_code(404);
    exit;
}

// verificam daca a fost trimisa recenzia
if(isset($_POST['hash']) && (isset($_POST['prenume'])) && (isset($_POST['nume'])) && (isset($_POST['continut_recenzie']))){
    // update recenzie in baza de date
    $sqlUpdateStr="UPDATE recenzii2 SET prenume='%s', nume='%s', recenzie='%s', data_recenzie='%s' WHERE hash='%s'";
    $sqlUpdate= sprintf($sqlUpdateStr, $_POST['prenume'],$_POST['nume'],$_POST['continut_recenzie'],$_POST['data_recenzie'], $hash);
    
    $connection->query($sqlUpdate) or die($connection->error);
    
    
    // stergem hash-ul din baza de date, ca sa nu permitem repost
    $connection->query(sprintf("UPDATE recenzii2 SET hash='' WHERE hash='%s'", $hash)) or die ($connection->error);
    // trimitem mail catre admin
    
    $to=$email_address;
    $subject='Mesaj din site';
    $message='O recenzie necesita validare';
    
    $success= sendSMTPEmail($to, $subject, $message);
    
    //
    $_OK=TRUE;
}

//var_dump($_POST);

// /////////////////////////////////////////////////////////////////////////////
// try to get content
//$query_text = "SELECT id, home FROM texte WHERE id = 1";
//$result = $connection->query($query_text) or die($connection->error);
//$row = $result->fetch_assoc();

$page_title = "Completare recenzie";
if($_OK==TRUE){
    $page_title = 'Va multumim!';
}
$page_subtitle = '';
//$content = is_null($row) ? "Content is missing" : $row['home'];

include './themes/automotive/template_parts/header.php';
$footer_scripts .= '';

$footer_scripts .= "<script src='https://www.google.com/recaptcha/api.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/jquery.validate.min.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/additional-methods.min.js'></script>";
$footer_scripts .= "<script type='text/javascript'>" . file_get_contents("./includes/validate_recenzie_add.js") . "</script>";

if($_OK==TRUE){
    $footer_scripts.='<script>setTimeout(function(){window.location.href = "'.BASE_PATH.'"},5000);</script>';
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


            <div class="with-sidebar col-lg-9 col-md-9 col-sm-12 col-xs-12 welcome padding-left-none md-padding-left-25 padding-bottom-40 scroll_effect fadeInUp information_head welcome-wrap">
                <div class="content-wrap car_listings">
                    <h3 class="information_head">Stimate client</h3>

                    <p>
                        Dorim sa va felicitam pentru achizitia recenta si sa va asiguram de aprecierea noastra sincera pentru alegerea unei colaborari cu AUTOLAND SRL, pentru care va multumim.
                    </p>
                    <p>
                        Din acest motiv va rugam foarte mult sa aveti bunavointa de a ne mentiona mai jos cateva impresii legate de experienta dumneavoastra cu AUTOLAND SRL:
                    </p>
                    <ul>
                        <li>
                            despre cum ati aflat de noi;
                        </li>
                        <li>
                            cum ati descrie colaborarea si procedurile firmei noastre? ati mai apela la noi daca ar mai fi nevoie?
                        </li>
                        <li>
                            cateva lucruri legate despre masina, ati gasit-o sub asteptari, peste sau exact asa cum trebuia?
                        </li> 
			<li>
                            daca ne-ati face vreo recomandare pe viitor?
                        </li>
                    </ul>

                    <div class="my-dropdown">
                        <form method="post" name="recenzie" id='recenzie' action="<?php echo BASE_PATH . 'completare_recenzie.php?hash=' . $hash; ?>">
                            <div id="result">
                                <?php
                                if($_OK==TRUE){
                                    echo 'Recenzia dumneavoastra a fost trimisa spre validare. Va multumim!';
                                }
                                ?>
                            </div>
                            <div class="my-dropdown">
                                <input type="text" class="form-control margin-bottom-25" id="Prenume" name="prenume" placeholder="Prenume" />
                            </div>
                            <div class="my-dropdown">
                                <input type="text" class="form-control margin-bottom-25" id="Nume" name="nume" placeholder="Nume" />
                            </div>
                            
                            <div class="my-dropdown">
                                <textarea maxlength="500" minlength="5" id="continut_recenzie" name="continut_recenzie" rows="5" width="100%"></textarea>
                                <div class="pull-right disabled" >
                                    Min: <span class="caractere_recenzie_min"></span>;
                                    Max: <span class="caractere_recenzie_max"></span>;
                                    Ramase: <span class="caractere_recenzie"></span>;
                                </div>
                            </div>
                            
                            
                            <input type="hidden" name="data_recenzie" id="data_recenzie" value="<?php echo date('Y-m-d h:i:s', time()); ?>">
                            <input type="hidden" name="hash" id="hash" value="<?php echo $hash;?>">
                            
                            <div class="my-dropdown form-group row">
                                <div class="g-recaptcha pull-left" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                            </div>
                            <div class="">
                                <input class="pull-left margin-top-25" id="submit_btn" type="submit" value="Trimite">
                            </div>
                        </form>
                    </div>
                    <div class="clear"></div>


                    <p>
                        Va multumim inca o data si va asiguram de intreaga noastra apreciere, dorindu-va mult spor in activitatea dumneavoastra!
                    </p>
                    <?php
//print_r($hashResult) ;
                    ?>
                </div>
            </div>
            <!--welcome ends-->
        </div>
    </div>

</section>

<?php
include './themes/automotive/template_parts/footer.php';
