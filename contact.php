<?php
/**
 * Contact page
 * 
 * This site requires php version >= 7.0
 * 
 * @copyright (c) 2018, Marius Ionel <webmaster@grupnet.ro>
 * @version 0.0.1
 */
// get config file;
include_once './includes/config.php';

// /////////////////////////////////////////////////////////////////////////////
// try to get content
$query_text = "SELECT id, contact FROM texte WHERE id = 1";

$result = $connection->query($query_text) or db_fail($connection);
$row = $result->fetch_assoc();

// Set optimized page title (direct in file for easier maintenance)
$page_title = "Contact Dealer Auto Iași | Autoland România";
$page_subtitle = '';

// Set SEO meta tags
require_once './includes/meta_tags.php';
setContactPageMeta();

$footer_scripts = "<script src='https://www.google.com/recaptcha/api.js' async></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/jquery.validate.min.js'></script>";
$footer_scripts .= "<script type='text/javascript' src='/themes/automotive/js/additional-methods.min.js'></script>";

$footer_scripts .= "<script type='text/javascript' async>" . file_get_contents("./includes/contact_footer.js") . "</script>";

$content = is_null($row) ? "Content is missing" : $row['contact'];

// Add JSON-LD structured data for SEO (LocalBusiness + Breadcrumbs)
require_once './includes/json_ld_schemas.php';
$breadcrumbs = [
    ['name' => 'Acasă', 'url' => BASE_PATH],
    ['name' => 'Contact', 'url' => BASE_PATH . 'contact.php']
];
$canonical_url = BASE_PATH . 'contact.php';
$contact_schemas = [
    getLocalBusinessSchema(),
    getBreadcrumbSchema($breadcrumbs),
    getWebPageSchema($canonical_url, $page_title, $meta_description ?? '')
];
$header_scripts .= getJsonLdString($contact_schemas);

// /////////////////////////////////////////////////////////////////////////////
// get header template part
include './themes/automotive/template_parts/header.php';
?>

<section class="content">
    <div class="container">
        <div class="inner-page">
            
            <div class="col-md-12 padding-none">
                <!--MAP-->
                <div class="find_map row clearfix">
                    <h2 class="margin-bottom-25 margin-top-none">SUNTEM AICI</h2>
                    <div class="map margin-vertical-30">
                        <iframe style='height: 390px' id='google-map-listing' class="contact" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1356.170548429924!2d27.543852130163575!3d47.1707578635384!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40cae4b8f493025f%3A0x9e0b250521007dad!2sAutoland+Romania!5e0!3m2!1sro!2sro!4v1544009637536" width="100%" height="390px" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
                <!--MAP-->
                <!--CONTACT INFORMATION-->
                <div class="row contacts margin-top-25">
                    <?php print $content;  ?>
                    <!--RIGHT INFORMATION-->
                    <div class="col-md-4 col-lg-offset-1 col-md-offset-1 padding-right-none xs-padding-left-none sm-padding-left-none xs-margin-top-30">
                        <div class="contact_wrapper information_head">
                            <h3 class="margin-bottom-25 margin-top-none">FORMULAR DE CONTACT</h3>
                            <div class="form_contact margin-bottom-20">
                                <div id="result"></div>
                                <form id="formular-contact" method="post" action="">
                                    <fieldset id="contact_form">
                                        <input type="hidden" name="form_title" value="Mesaj din formularul de contact">
                                        <input type="text" name="name" class="form-control margin-bottom-25" placeholder="Nume si prenume" />
 					<input type="tel" name="telefon" class="form-control margin-bottom-25" placeholder="Telefon"/>
                                        <input type="email" name="email" class="form-control margin-bottom-25" placeholder="Email" />
                                        <input type="text" name="subiect" class="form-control margin-bottom-25" placeholder="Subiect" />
                                        <textarea name="mesaj" class="form-control margin-bottom-25 contact_textarea" placeholder="Mesaj" rows="7"></textarea>

                                        <div class="form-group row">
                                            <div class="g-recaptcha pull-left" data-sitekey="6LcbK38UAAAAAMAFTZuUcAh346Pp7PDT_YTO-Veq"></div>
                                            <input type="hidden" class="hiddenRecaptcha required" name="hiddenRecaptcha" id="hiddenRecaptcha">
                                        </div>

                                        <input id="submit_btn" type="submit" value="Trimite mesaj">
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--RIGHT INFORMATION-->

                </div>
                <!---CONTACT INFORMATION-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!--container ends--> 
</section>
<!--content ends-->

<?php
// get footer template part
include './themes/automotive/template_parts/footer.php';
